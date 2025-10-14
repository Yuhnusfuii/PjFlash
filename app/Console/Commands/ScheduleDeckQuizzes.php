<?php

namespace App\Console\Commands;

use App\Models\Deck;
use App\Models\Quiz;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class ScheduleDeckQuizzes extends Command
{
    protected $signature = 'quiz:schedule-weekly {--mode=mixed}';
    protected $description = 'Create weekly MCQ quizzes for each user’s decks (due next Monday).';

    public function handle(): int
    {
        $mode = (string) $this->option('mode') ?: 'mixed';
        $due  = CarbonImmutable::now()->next(CarbonImmutable::MONDAY)->startOfDay()->addHours(9);

        Deck::withCount('items')->whereHas('items')
            ->chunkById(500, function ($decks) use ($due, $mode) {
                foreach ($decks as $deck) {
                    Quiz::firstOrCreate(
                        ['user_id' => $deck->user_id, 'deck_id' => $deck->id, 'due_at' => $due],
                        ['mode' => $mode]
                    );
                }
            });

        $this->info('Scheduled weekly quizzes for '.$due->toDateTimeString());
        return self::SUCCESS;
    }
}
