<?php

namespace App\Commands\Task;

use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{table, warning, error, confirm};

class DeleteTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:delete {task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a task';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = Task::find($this->argument('task'));

        warning('You are about to delete the following task:');

        table([
            'ID',
            'Name',
            'Project',
            'Priority',
            'Tags',
            'Due Date',
            'Updated At',
        ], [
            [
                $task->id,
                $task->name,
                $task?->project->name ?? 'No project',
                $task->priority,
                $task->tags->pluck('name')->join(', '),
                $task->due_date,
                $task->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);

        if (!confirm('Are you sure you want to delete this task?', false)) {
            error('Task deletion cancelled');
            return;
        }

        $this->task('Deleting task', function () use ($task) {
            return $task->delete();
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
