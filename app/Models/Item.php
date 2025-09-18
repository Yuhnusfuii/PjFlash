<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ItemType;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['deck_id','type','front','back','data'];
    protected $guarded = [];
    protected $casts = [
        'data' => 'array',
        'type' => ItemType::class, // enum: flashcard|mcq|matching
    ];

    public function deck(){ return $this->belongsTo(Deck::class); }
    public function reviewStates(){ return $this->hasMany(ReviewState::class); }
}
