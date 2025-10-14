<?php

use App\Console\Commands\ScheduleDeckQuizzes;
use App\Models\Deck;
use App\Models\Item;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Carbon\CarbonImmutable;

uses(RefreshDatabase::class);

test('schedule weekly creates quizzes for decks with items only and is idempotent', function () {
    $now = CarbonImmutable::parse('2025-10-08 10:00:00');
    Carbon\Carbon::setTestNow($now);

    $u = User::factory()->create();
    $dWith = Deck::create(['user_id'=>$u->id,'name'=>'with items']);
    $dEmpty = Deck::create(['user_id'=>$u->id,'name'=>'empty']);
    Item::create(['deck_id'=>$dWith->id,'front'=>'A','back'=>'B','type'=>'flashcard']);

    Artisan::registerCommand(app(ScheduleDeckQuizzes::class));

    Artisan::call('quiz:schedule-weekly', ['--mode' => 'mixed']);
    Artisan::call('quiz:schedule-weekly', ['--mode' => 'mixed']); // idempotent

    expect(Quiz::where('deck_id',$dWith->id)->count())->toBe(1);
    expect(Quiz::where('deck_id',$dEmpty->id)->count())->toBe(0);

    $quiz = Quiz::first();
    expect($quiz->due_at->isMonday())->toBeTrue();
});
