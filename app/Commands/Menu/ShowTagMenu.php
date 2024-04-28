<?php

namespace App\Commands\Menu;

use App\Commands\Tag\DeleteTag;
use App\Commands\Tag\EditTag;
use App\Models\Tag;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ShowTagMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:show-menu {tag}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show tag menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tag = Tag::findOrFail($this->argument('tag'));

        $option = $this->menu($tag->name, [
            'edit' => 'Edit tag',
            'delete' => 'Delete tag',
        ])
        ->addLineBreak('-')
        ->addStaticItem(sprintf('ID: %d. %s. %s', $tag->id, $tag->name, $tag->description))
        ->addStaticItem(sprintf('Created at: %s (%s)', $tag->created_at->format('Y-m-d H:i:s'), $tag->created_at->diffForHumans()))
        ->addStaticItem(sprintf('Updated at: %s (%s)', $tag->updated_at->format('Y-m-d H:i:s'), $tag->updated_at->diffForHumans()))
        ->addLineBreak('-')
        ->addStaticItem(sprintf('Tasks: %d', $tag->tasks->count()))
        ->addLineBreak('-')
        ->open();

        if ($option === 'edit') {
            $this->call(EditTag::class, [
                'tag' => $tag->id,
            ]);
            $this->handle();
        }

        if ($option === 'delete') {
            $this->call(DeleteTag::class, [
                'tag' => $tag->id,
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
