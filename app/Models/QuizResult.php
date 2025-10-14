<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizResult extends Model
{
    protected $fillable = [
        'quiz_id','item_id','direction','question','correct','picked','is_correct',
    ];

    public function quiz(): BelongsTo { return $this->belongsTo(Quiz::class); }
    public function item(): BelongsTo { return $this->belongsTo(Item::class); }
}
