<?php

namespace App\Commands\Menu;

use App\Commands\Project\AddProject;
use App\Models\Project;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MainProjectMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Main project menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = [
            'create' => 'Create a project',
        ];
        $projects = Project::all('id', 'name')->keyBy('id')
            ->map(fn ($project) => $project->name)
            ->toArray();

        $options = array_combine(array_keys($options), $options) + $projects;

        $selectedOption = $this->menu('Projects', $options)->open();

        if ($selectedOption === null) {
            return $this->call(MainMenu::class);
        }

        if ($selectedOption === 'create') {
            $this->call(AddProject::class);
            return $this->handle();
        }

        $this->call(ShowProjectMenu::class, [
            'project' => $selectedOption
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
