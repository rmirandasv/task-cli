<?php

namespace App\Commands\Menu;

use App\Commands\Project\DeleteProject;
use App\Commands\Project\EditProject;
use App\Models\Project;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ShowProjectMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:show-menu {project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show project menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $project = Project::findOrFail($this->argument('project'));

        $option = $this->menu($project->name, [
            'edit' => 'Edit project',
            'delete' => 'Delete project',
        ])
        ->addLineBreak('-')
        ->addStaticItem(sprintf('ID: %d. %s. %s', $project->id, $project->name, $project->description))
        ->addStaticItem(sprintf('Created at: %s (%s)', $project->created_at->format('Y-m-d H:i:s'), $project->created_at->diffForHumans()))
        ->addStaticItem('Updated at: ' . $project->updated_at)
        ->addLineBreak('-')
        ->addStaticItem(sprintf('Tasks: %d', $project->tasks->count()))
        ->addLineBreak('-')
        ->open();

        if ($option === 'edit') {
            $this->call(EditProject::class, [
                'project' => $project->id,
            ]);
            $this->handle();
        }

        if ($option === 'delete') {
            $this->call(DeleteProject::class, [
                'project' => $project->id,
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
