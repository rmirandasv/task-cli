<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\{info};

class Installation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the initial database';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $databaseDirectoryExist = false;
        if (file_exists($_SERVER['HOME'] . '/.task-cli')) {
            $databaseDirectoryExist = true;
        }

        if (!$databaseDirectoryExist) {
            $this->task('Creating the database directory...', function () {
                if (!file_exists($_SERVER['HOME'] . '/.task-cli')) {
                    mkdir($_SERVER['HOME'] . '/.task-cli');
                    return true;
                }
                return false;
            });
        }

        $databaseExist = false;
        if (file_exists($_SERVER['HOME'] . '/.task-cli/database.sqlite')) {
            $databaseExist = true;
        }

        $migrationsTableExist = false;
        if ($databaseExist) {
            $this->task('Checking if the migrations table exists...', function () use (&$migrationsTableExist) {
                if (DB::connection()->getPdo()->query('SELECT name FROM sqlite_master WHERE type="table" AND name="migrations"')->fetch()) {
                    $migrationsTableExist = true;
                    return true;
                }
                return false;
            });
        }

        if ($migrationsTableExist) {
            info('Migrations table already exists. This command is only for the initial setup.');
            return;
        }

        $this->call(MigrateCommand::class, [
            '--force' => true,
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
