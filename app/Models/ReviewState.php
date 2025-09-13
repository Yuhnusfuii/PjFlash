<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewState extends Model
{
    protected $fillable = [
    'user_id','item_id','ease_factor','interval_days','due_at',
    'repetitions','lapses','last_reviewed_at','suspended','stability'
];

}
