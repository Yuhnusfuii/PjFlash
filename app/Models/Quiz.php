<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = ['user_id','deck_id','mode','payload','due_at','completed_at'];
    protected $casts = ['payload'=>'array','due_at'=>'datetime','completed_at'=>'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function deck(): BelongsTo { return $this->belongsTo(Deck::class); }
    public function results(): HasMany { return $this->hasMany(QuizResult::class); }
}
