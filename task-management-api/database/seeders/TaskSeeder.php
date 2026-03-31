<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Seed the tasks table with sample data for testing.
     */
    public function run(): void
    {
        $tasks = [
            [
                'title'    => 'Set up CI/CD pipeline',
                'due_date' => now()->addDays(1)->format('Y-m-d'),
                'priority' => 'high',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Write unit tests for auth module',
                'due_date' => now()->addDays(2)->format('Y-m-d'),
                'priority' => 'high',
                'status'   => 'in_progress',
            ],
            [
                'title'    => 'Update project documentation',
                'due_date' => now()->addDays(3)->format('Y-m-d'),
                'priority' => 'medium',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Fix login bug on mobile',
                'due_date' => now()->addDays(1)->format('Y-m-d'),
                'priority' => 'high',
                'status'   => 'done',
            ],
            [
                'title'    => 'Design onboarding flow',
                'due_date' => now()->addDays(5)->format('Y-m-d'),
                'priority' => 'medium',
                'status'   => 'in_progress',
            ],
            [
                'title'    => 'Clean up unused dependencies',
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'priority' => 'low',
                'status'   => 'pending',
            ],
            [
                'title'    => 'Review pull requests',
                'due_date' => now()->format('Y-m-d'),
                'priority' => 'medium',
                'status'   => 'done',
            ],
            [
                'title'    => 'Deploy staging environment',
                'due_date' => now()->addDays(4)->format('Y-m-d'),
                'priority' => 'high',
                'status'   => 'pending',
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }

        $this->command->info('✅ Tasks seeded successfully (' . count($tasks) . ' tasks).');
    }
}
