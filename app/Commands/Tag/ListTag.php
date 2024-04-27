<?php

namespace App\Commands\Tag;

use App\Models\Tag;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{table, info};

class ListTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all tags';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tags = Tag::orderBy('name')
            ->get(['id', 'name', 'slug', 'description'])
            ->map(fn ($tag) => array_values($tag->toArray()))
            ->toArray();

        info('List of tags');

        table([
            'ID',
            'Name',
            'Slug',
            'Description',
        ], $tags);
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
