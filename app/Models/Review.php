<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','item_id','ease','interval','repetitions','due_at','last_reviewed_at',
    ];

    protected $casts = [
        'ease' => 'float',
        'interval' => 'integer',
        'repetitions' => 'integer',
        'due_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
    ];

    public function item(){ return $this->belongsTo(Item::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
