<?php

namespace App\Commands\Tag;

use App\Models\Tag;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\{info, form, table};

class EditTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tag:edit {tag} {--name=} {--slug=} {--description=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit a tag';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tag = Tag::findOrFail($this->argument('tag'));

        info('Edit tag');

        if ($this->option('name')) {
            $tag->name = $this->option('name');
        }

        if ($this->option('slug')) {
            $tag->slug = $this->option('slug');
        }

        if ($this->option('description')) {
            $tag->description = $this->option('description');
        }

        if (!$tag->isDirty()) {
            $data = form()
                ->text(
                    label: 'Name',
                    default: $tag->name,
                    required: true,
                    validate: fn (string $value) => match (true) {
                        strlen($value) > 45 => 'Name must be less than 45 characters',
                        default => null,
                    },
                    name: 'name',
                )
                ->text(
                    label: 'Slug',
                    default: $tag->slug,
                    required: true,
                    validate: fn (string $value) => match (true) {
                        Tag::where('slug', $value)->where('id', '!=', $tag->id)->exists() => 'Slug already exists',
                        strlen($value) > 45 => 'Slug must be less than 45 characters',
                        default => null,
                    },
                    name: 'slug',
                )
                ->textarea(
                    label: 'Description',
                    default: $tag->description,
                    name: 'description',
                    validate: fn (string $value) => match (true) {
                        strlen($value) > 255 => 'Description must be less than 255 characters',
                        default => null,
                    },
                )
                ->submit();

            $tag->name = $data['name'];
            $tag->slug = $data['slug'];
            $tag->description = $data['description'];
        }

        $this->task('Updating tag', function () use ($tag) {

            $saved = $tag->save();
            
            table([
                'ID',
                'Name',
                'Slug',
                'Description',
            ], [
                [
                    $tag->id,
                    $tag->name,
                    $tag->slug,
                    $tag->description,
                ]
            ]);

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
