<?php

namespace App\Commands\Tag;

use App\Models\Tag;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{form, table, info};

class AddTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tag';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info('Create a new tag');
        $data = form()
            ->text(
                label: 'Name',
                required: true,
                validate: fn (string $value) => match (true) {
                    $value === '' => 'Name is required.',
                    strlen($value) > 45 => 'Name must be less than 45 characters.',
                    default => null,
                },
                name: 'name'
            )
            ->text(
                label: 'Slug',
                required: true,
                validate: fn (string $value) => match (true) {
                    $value === '' => 'Slug is required.',
                    strlen($value) > 45 => 'Slug must be less than 45 characters.',
                    default => null,
                },
                name: 'slug'
            )
            ->textarea(
                label: 'Description',
                required: false,
                validate: fn (string $value) => match (true) {
                    strlen($value) > 255 => 'Description must be less than 255 characters.',
                    default => null,
                },
                name: 'description'
            )
            ->submit();

        $this->task("Creating tag {$data['name']}", function () use ($data) {
            $tag = Tag::create($data);

            table(
                ['ID', 'Name', 'Slug', 'Description'],
                [$tag->only(['id', 'name', 'slug', 'description'])]
            );

            return true;
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
