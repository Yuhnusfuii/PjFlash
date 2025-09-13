<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
    'user_id','item_id','rating','interval_days','ease_factor',
    'reviewed_at','next_due_at','duration_ms','meta'
];

}
