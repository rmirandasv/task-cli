<?php

namespace App\Commands;

use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class InteractiveMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interactive-menu';

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
        $option = $this->menu('Choose an option', [
            'List tasks',
            'Add a task',
            'Edit a task',
            'Delete a task',
            'Exit',
        ])
            ->setForegroundColour('green')
            ->setBackgroundColour('black')
            ->setWidth(200)
            ->setPadding(10)
            ->setMargin(5)
            ->setExitButtonText("Exit")
            ->addLineBreak('-')
            ->addStaticItem('Projects')
            ->addLineBreak('')
            ->addStaticItem('Exit')
            ->addLineBreak('-')
            ->open();

        if ($option === null) {
            $this->info('Goodbye!');
            return;
        }

        if ($option === 0) {
            $tasks = Task::all();

            $options = $tasks->map(function ($task) {
                return $task->name;
            })->toArray();

            if (empty($options)) {
                $this->menu('No tasks found', ['Exit'])
                    ->setForegroundColour('red')
                    ->setBackgroundColour('black')
                    ->setWidth(200)
                    ->setPadding(10)
                    ->setMargin(5)
                    ->setExitButtonText("Exit")
                    ->addLineBreak('-')
                    ->addStaticItem('Projects')
                    ->addLineBreak('')
                    ->addStaticItem('Exit')
                    ->addLineBreak('-')
                    ->open();

                return $this->handle();
            }

            $option = $this->menu('Choose a task to edit', $options)
                ->setForegroundColour('green')
                ->setBackgroundColour('black')
                ->setWidth(200)
                ->setPadding(10)
                ->setMargin(5)
                ->setExitButtonText("Exit")
                ->addLineBreak('-')
                ->addStaticItem('Projects')
                ->addLineBreak('')
                ->addStaticItem('Exit')
                ->addLineBreak('-')
                ->open();
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
