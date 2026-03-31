<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * POST /api/tasks
     * Create a new task.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'title'    => $request->title,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'status'   => 'pending', // always starts as pending
        ]);

        return response()->json([
            'message' => 'Task created successfully.',
            'task'    => $task,
        ], 201);
    }

    /**
     * GET /api/tasks
     * List all tasks sorted by priority (high→low) then due_date asc.
     * Optional ?status= filter.
     */
    public function index(Request $request): JsonResponse
    {
        $statusFilter = $request->query('status');

        // Validate status query param if provided
        if ($statusFilter && !in_array($statusFilter, ['pending', 'in_progress', 'done'])) {
            return response()->json([
                'message' => 'Invalid status filter. Must be one of: pending, in_progress, done.',
            ], 422);
        }

        $tasks = Task::sorted()
                     ->ofStatus($statusFilter)
                     ->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found.',
                'tasks'   => [],
            ], 200);
        }

        return response()->json([
            'total' => $tasks->count(),
            'tasks' => $tasks,
        ], 200);
    }

    /**
     * PATCH /api/tasks/{id}/status
     * Advance a task's status forward only: pending → in_progress → done.
     */
    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        $newStatus = $request->status;

        // Enforce forward-only status transitions
        if (!$task->canTransitionTo($newStatus)) {
            $allowed = Task::$statusFlow[$task->status] ?? null;

            return response()->json([
                'message'          => 'Invalid status transition.',
                'current_status'   => $task->status,
                'allowed_next'     => $allowed ?? 'none (task is already done)',
                'requested_status' => $newStatus,
            ], 422);
        }

        $task->update(['status' => $newStatus]);

        return response()->json([
            'message' => 'Task status updated.',
            'task'    => $task->fresh(),
        ], 200);
    }

    /**
     * DELETE /api/tasks/{id}
     * Only tasks with status "done" may be deleted.
     */
    public function destroy(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        if ($task->status !== 'done') {
            return response()->json([
                'message'        => 'Only completed (done) tasks can be deleted.',
                'current_status' => $task->status,
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ], 200);
    }

    /**
     * GET /api/tasks/report?date=YYYY-MM-DD
     * Bonus: return counts of tasks grouped by priority and status for a given date.
     */
    public function report(Request $request): JsonResponse
    {
        $date = $request->query('date');

        if (!$date || !strtotime($date)) {
            return response()->json([
                'message' => 'A valid date query parameter is required (format: YYYY-MM-DD).',
            ], 422);
        }

        // Validate date format strictly
        if (!\DateTime::createFromFormat('Y-m-d', $date)) {
            return response()->json([
                'message' => 'Date must be in YYYY-MM-DD format.',
            ], 422);
        }

        $priorities = ['high', 'medium', 'low'];
        $statuses   = ['pending', 'in_progress', 'done'];

        $tasks = Task::whereDate('due_date', $date)->get();

        // Build summary matrix
        $summary = [];
        foreach ($priorities as $priority) {
            foreach ($statuses as $status) {
                $summary[$priority][$status] = $tasks
                    ->where('priority', $priority)
                    ->where('status', $status)
                    ->count();
            }
        }

        return response()->json([
            'date'    => $date,
            'summary' => $summary,
        ], 200);
    }
}
