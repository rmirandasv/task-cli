<?php

namespace App\Commands\Menu;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{info};

class MainMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage tasks from the main menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $menu = $this->menu('Choose an option', [
            'task' => 'Tasks',
            'project' => 'Projects',
            'tag' => 'Tags',
        ])->open();

        if ($menu === null) {
            info('Goodbye!');
            return;
        }

        if ($menu === 'project') {
            $this->call(MainProjectMenu::class);
        }

        if ($menu === 'tag') {
            $this->call(MainTagMenu::class);
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
