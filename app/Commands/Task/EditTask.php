<?php

namespace App\Commands\Task;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{form, table, info};

class EditTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:edit {task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit a task';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = Task::findOrFail($this->argument('task'));

        info('Edit task');

        $data = form()
            ->text(
                label: 'Name',
                placeholder: 'Task name',
                default: $task->name,
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 3 => 'Name must be at least 3 characters long',
                    strlen($value) > 100 => 'Name must not be longer than 100 characters',
                    default => null,
                },
                name: 'name'
            )
            ->textarea(
                label: 'Description',
                placeholder: 'Task description',
                default: $task->description,
                validate: fn (string $value) => strlen($value) > 255 ? 'Description must not be longer than 255 characters' : null,
                name: 'description'
            )
            ->select(
                label: 'Project',
                default: $task->project_id,
                options: Project::count() ? array_merge([null => 'No project'], Project::pluck('name', 'id')->toArray()) : [null => 'No projects found'],
                name: 'project_id'
            )
            ->select(
                label: 'Priority',
                default: $task->priority,
                options: [
                    '0' => 'Low',
                    '1' => 'Medium',
                    '2' => 'High',
                ],
                name: 'priority'
            )
            ->select(
                label: 'Status',
                default: $task->status,
                options: [
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                ],
                name: 'status'
            )
            ->text(
                label: 'Due date',
                default: $task->due_date,
                placeholder: 'YYYY-MM-DD',
                validate: fn (string $value) => !preg_match('/\d{4}-\d{2}-\d{2}/', $value) ? 'Invalid date format' : null,
                name: 'due_date'
            )
            ->multiselect(
                label: 'Tags',
                default: $task->tags->pluck('id')->toArray(),
                options: Tag::count() ? Tag::pluck('name', 'id')->toArray() : [null => 'No tags found'],
                name: 'tags'
            )
            ->submit();

        $this->task('Updating task', function () use ($task, $data) {
            $updated = $task->update($data);
            $task->tags()->sync($data['tags']);
            $task->refresh();
            table([
                'ID',
                'Name',
                'Priority',
                'Status',
                'Project',
                'Tags'
            ], [[
                $task->id,
                $task->name,
                $task->priority,
                $task->status,
                $task->project?->name ?? 'None',
                $task->tags->pluck('name')->join(', ') ?: 'None',
            ]]);
            return $updated;
        });

    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
