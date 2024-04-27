<?php

namespace App\Commands\Project;

use App\Models\Project;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\form;
use function Laravel\Prompts\table;
use function Laravel\Prompts\info;

class AddProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = form() 
            ->text(
                label: 'Project name',
                placeholder: 'Enter the project name',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 2 => 'The project name must be at least 3 characters long',
                    strlen($value) > 30 => 'The project name must not exceed 30 characters',
                    default => null,
                },
                name: 'name',
            )
            ->textarea(
                label: 'Description',
                placeholder: 'Enter the project description',
                validate: fn (string $value) => match (true) {
                    strlen($value) > 255 => 'The description must not exceed 255 characters',
                    default => null,
                },
                name: 'description',
            )
            ->submit();

        $project = Project::create($data);

        info('Project created successfully');

        table([
            'ID', 
            'Name', 
            'Description', 
            'Created at'
        ], [$project->only([
            'id', 
            'name', 
            'description', 
            'created_at'
        ])]);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
