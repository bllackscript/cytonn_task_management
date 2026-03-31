<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Task Management API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically by Laravel.
|
*/

// IMPORTANT: /report must come BEFORE the {id} routes to avoid
// Laravel mistaking "report" for a task ID.
Route::get('/tasks/report', [TaskController::class, 'report']);

Route::get('/tasks',                    [TaskController::class, 'index']);
Route::post('/tasks',                   [TaskController::class, 'store']);
Route::patch('/tasks/{id}/status',      [TaskController::class, 'updateStatus']);
Route::delete('/tasks/{id}',            [TaskController::class, 'destroy']);
