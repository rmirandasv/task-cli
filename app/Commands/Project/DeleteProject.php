<?php

namespace App\Commands\Project;

use App\Models\Project;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{info, warning, table, confirm, error};

class DeleteProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:delete {project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $project = Project::findOrFail($this->argument('project'));

        warning("You are about to delete the project: {$project->name}. All associated tasks will be deleted as well.");

        table([
            'ID',
            'Name',
            'Tasks',
        ], [
            [
                $project->id, 
                $project->name, 
                $project->tasks->count()
            ]
        ]);

        if (!confirm('Are you sure you want to delete this project?', false)) {
            error('Operation cancelled.');
            return;
        }

        $this->task('Deleting project', function () use ($project) {
            $project->delete();
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
