<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'deck_id',
        'type',
        'front',
        'back',
        'data',
    ];

    protected $casts = [
        'data' => 'array', // NOTE: cần cho MCQ/Matching payload
    ];

    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    public function reviewStates()
    {
        return $this->hasMany(ReviewState::class);
    }
}
