<?php

namespace App\Commands\Menu;

use App\Commands\Task\AddTask;
use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MainTaskMenu extends Command
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
    protected $description = 'Main task menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = [
            'create' => 'Create a task',
        ];

        $tasks = Task::all('id', 'name')->keyBy('id')
            ->map(fn ($task) => $task->name)
            ->toArray();

        $options = array_combine(array_keys($options), $options) + $tasks;

        $selectedOption = $this->menu('Tasks', $options)->open();

        if ($selectedOption === null) {
            return $this->call(MainMenu::class);
        }

        if ($selectedOption === 'create') {
            $this->call(AddTask::class);
            return $this->handle();
        }

        $this->call(ShowTaskMenu::class, [
            'task' => $selectedOption
        ]);

        $this->handle();
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
