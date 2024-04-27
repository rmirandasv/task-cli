<?php

namespace App\Commands\Tag;

use App\Models\Tag;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{warning, confirm, error, table};

class DeleteTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:delete {tag}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = Tag::findOrFail($this->argument('tag'));

        warning("You are about to delete the tag: {$task->name}. Related tasks will not be deleted.");

        table(['ID', 'Name', 'Tasks'], [[$task->id, $task->name, $task->tasks->count()]]);

        if (!confirm('Do you want to continue?', false)) {
            error('Operation cancelled');
            return;
        }

        $this->task('Deleting tag', function () use ($task) {
            return $task->delete();
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
