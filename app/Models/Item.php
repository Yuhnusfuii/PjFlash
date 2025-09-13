<?php

namespace App\Models;

use App\Enums\ItemType; // 👈 thêm dòng này
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Item extends Model
{
    protected $fillable = ['deck_id','type','front','back','data','hint','position'];

    protected $casts = [
        'type' => ItemType::class, // 👈 cast sang backed enum
        'data' => 'array',
    ];

    // ...
}
