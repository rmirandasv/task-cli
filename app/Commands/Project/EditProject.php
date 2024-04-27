<?php

namespace App\Commands\Project;

use App\Models\Project;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{form, info, table};

class EditProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:edit {project} {--name=} {--description=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit a project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $project = Project::findOrFail($this->argument('project'));

        if ($this->option('name')) {
            $project->name = $this->option('name');
        }

        if ($this->option('description')) {
            $project->description = $this->option('description');
        }

        if (!$project->isDirty()) {
            $data = form()
                ->text(
                    label: 'Name',
                    placeholder: 'Project name',
                    default: $project->name,
                    required: true,
                    validate: fn (string $value) => match (true) {
                        strlen($value) < 3 => 'The name must be at least 3 characters long',
                        strlen($value) > 255 => 'The name must be at most 255 characters long',
                        default => null,
                    },
                    name: 'name',
                )
                ->textarea(
                    label: 'Description',
                    placeholder: 'Project description',
                    default: $project->description,
                    validate: fn (string $value) => match (true) {
                        strlen($value) < 3 => 'The description must be at least 3 characters long',
                        strlen($value) > 65535 => 'The description must be at most 65535 characters long',
                        default => null,
                    },
                    name: 'description',
                )
                ->submit();

            $project->name = $data['name'];
            $project->description = $data['description'];
        }

        $project->save();

        info('Project updated successfully');

        table([
            'ID',
            'Name',
            'Description',
            'Updated At',
        ], [
            [
                $project->id,
                $project->name,
                $project->description,
                $project->updated_at,
            ],
        ]);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
