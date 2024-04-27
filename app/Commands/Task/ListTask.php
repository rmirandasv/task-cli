<?php

namespace App\Commands\Task;

use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{info, table};

class ListTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('Tasks');

        $tasks = Task::with('project', 'tags')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($task) {
                return [
                    $task->id,
                    $task->name,
                    $task->priority,
                    $task->status,
                    $task->project->name,
                    $task->tags()->get()->pluck('name')->join(', ')
                ];
            })
            ->toArray();

        table([
            'ID',
            'Name',
            'Priority',
            'Status',
            'Project',
            'Tags'
        ], $tasks);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
