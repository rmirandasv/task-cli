<?php

namespace App\Commands\Menu;

use App\Commands\Tag\AddTag;
use App\Models\Tag;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class MainTagMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Main tag menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = [
            'create' => 'Create a tag',
        ];
        $tags = Tag::all('id', 'name')->keyBy('id')
            ->map(fn ($tag) => $tag->name)
            ->toArray();

        $options = array_combine(array_keys($options), $options) + $tags;

        $selectedOption = $this->menu('Tags', $options)->open();

        if ($selectedOption === null) {
            return $this->call(MainMenu::class);
        }

        if ($selectedOption === 'create') {
            $this->call(AddTag::class);
            return $this->handle();
        }

        $this->call(ShowTagMenu::class, [
            'tag' => $selectedOption
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
