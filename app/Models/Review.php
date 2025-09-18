<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class Review extends Model
{
    protected $fillable = [
        'user_id','item_id','rating','interval_days','ease_factor','reviewed_at','next_due_at','duration_ms','meta'
    ];
    protected $guarded = [];
    protected $casts = [
        'rating' => 'integer',
        'interval_days' => 'integer',
        'ease_factor' => 'float',
        'reviewed_at' => 'datetime',
        'next_due_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function item(): BelongsTo { return $this->belongsTo(Item::class); }
}
