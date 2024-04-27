<?php

namespace App\Commands\Project;

use App\Models\Project;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{table, info, select};

class ListProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all projects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('List of all projects');

        $prrojects = Project::orderBy('id', 'desc')->get();

        table([
            'ID',
            'Name',
            'Description',
            'Created At',
            'Updated At',
        ], $prrojects->map(function ($project) {
            return [
                $project->id,
                $project->name,
                $project->description,
                $project->created_at->format('Y-m-d H:i:s'),
                $project->updated_at->format('Y-m-d H:i:s'),
            ];
        })->toArray());
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
