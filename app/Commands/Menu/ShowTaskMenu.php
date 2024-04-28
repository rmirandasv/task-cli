<?php

namespace App\Commands\Menu;

use App\Commands\Task\DeleteTask;
use App\Commands\Task\EditTask;
use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ShowTaskMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:show-menu {task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show task menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = Task::findOrFail($this->argument('task'));

        $option = $this->menu($task->name, [
            'edit' => 'Edit task',
            'delete' => 'Delete task',
        ])
        ->addLineBreak('-')
        ->addStaticItem(sprintf('ID: %d. %s. %s', $task->id, $task->name, $task->description))
        ->addStaticItem(sprintf('Priority: %s, Status: %s', $task->priority, $task->status))
        ->addStaticItem(sprintf('Created at: %s (%s)', $task->created_at->format('Y-m-d H:i:s'), $task->created_at->diffForHumans()))
        ->addStaticItem(sprintf('Updated at: %s (%s)', $task->updated_at->format('Y-m-d H:i:s'), $task->updated_at->diffForHumans()))
        ->addLineBreak('-')
        ->addStaticItem(sprintf('Project: %s', $task->project->name))
        ->addStaticItem(sprintf('Tags: %s', $task->tags->pluck('name')->implode(', ')))
        ->addLineBreak('-')
        ->open();

        if ($option === 'edit') {
            $this->call(EditTask::class, [
                'task' => $task->id,
            ]);
            $this->handle();
        }

        if ($option === 'delete') {
            $this->call(DeleteTask::class, [
                'task' => $task->id,
            ]);
            return;
        }

        return;
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
