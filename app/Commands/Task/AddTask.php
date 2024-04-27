<?php

namespace App\Commands\Task;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use function Laravel\Prompts\{form, table};

class AddTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:add {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new task';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->argument('name')) {
            $this->task('Creating task', function () {
                $task = new Task();
                $task->name = $this->argument('name');
                return $task->save();
            });
        }

        $data = form()
            ->text(
                label: 'Name',
                placeholder: 'Task name',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 3 => 'Name must be at least 3 characters long',
                    strlen($value) > 100 => 'Name must not be longer than 100 characters',
                    default => null,
                },
                name: 'name'
            )
            ->textarea(
                label: 'Description',
                placeholder: 'Task description',
                validate: fn (string $value) => strlen($value) > 255 ? 'Description must not be longer than 255 characters' : null,
                name: 'description'
            )
            ->select(
                label: 'Project',
                options: Project::count() ? Project::pluck('name', 'id')->toArray() : [null => 'No projects found'],
                name: 'project_id'
            )
            ->select(
                label: 'Priority',
                options: [
                    '0' => 'Low',
                    '1' => 'Medium',
                    '2' => 'High',
                ],
                name: 'priority'
            )
            ->select(
                label: 'Status',
                options: [
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                ],
                name: 'status'
            )
            ->text(
                label: 'Due date',
                placeholder: 'YYYY-MM-DD',
                validate: fn (string $value) => !preg_match('/\d{4}-\d{2}-\d{2}/', $value) ? 'Invalid date format' : null,
                name: 'due_date'
            )
            ->multiselect(
                label: 'Tags',
                options: Tag::count() ? Tag::pluck('name', 'id')->toArray() : [null => 'No tags found'],
                name: 'tags'
            )
            ->submit();

        $this->task('Creating task', function () use ($data) {
            $task = new Task();
            $task->fill($data);
            $saved = $task->save();
            $task->tags()->sync($data['tags']);

            table([
                'ID',
                'Name',
                'Priority',
                'Status',
                'Project',
                'Tags'
            ], [[
                $task->id,
                $task->name,
                $task->priority,
                $task->status,
                $task->project->name,
                $task->tags()->get()->pluck('name')->join(', ')
            ]]);

            return $saved;
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
