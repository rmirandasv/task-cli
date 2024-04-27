<?php

namespace App\Commands;

use App\Commands\Task\AddTask;
use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{info};

class InteractiveMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage your tasks with an interactive menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $option = $this->rootMenu();

        if ($option === 0) {
            $this->tasksMenu();
        }
    }

    public function tasksMenu()
    {
        $tasks = Task::all()->keyBy('id')->map(function ($task) {
            return $task->name;
        })->toArray();

        $tasks = array_merge(['create' => 'Create a new task'], $tasks);

        $taskId = $this->menu('Tasks', $tasks)->open();

        if ($taskId === 'create') {
            $this->call(AddTask::class);
            $this->tasksMenu();
        }

        if ($taskId === null) {
            $this->rootMenu();
        }
    }

    protected function rootMenu(): ?int
    {
        return $this->menu('Welcome to task-cli', [
            'Tasks',
            'Projects',
            'Tags',
        ])
        ->open();
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
