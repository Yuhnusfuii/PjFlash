<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewState extends Model
{
    use HasFactory;

    // NOTE: giữ mở để fill cả các tên cũ và mới (tránh vỡ chỗ khác trong dự án)
    protected $guarded = [];

    protected $fillable = [
        'user_id', 'item_id',

        // schema mới (ưu tiên dùng)
        'ease_factor', 'interval_days', 'repetitions', 'lapses',
        'due_at', 'last_reviewed_at',

        // tên cũ (nếu chỗ khác trong app có dùng)
        'ease', 'interval',
    ];

    protected $casts = [
        // mới
        'ease_factor'      => 'float',
        'interval_days'    => 'integer',
        'repetitions'      => 'integer',
        'lapses'           => 'integer',
        'due_at'           => 'datetime',
        'last_reviewed_at' => 'datetime',
        // cũ
        'ease'             => 'float',
        'interval'         => 'integer',
    ];

    public function item() { return $this->belongsTo(Item::class); }
    public function user() { return $this->belongsTo(User::class); }

    /** Card đến hạn tại thời điểm $at (default: now) */
    public function scopeDue($q, ?\Carbon\CarbonInterface $at = null)
    {
        $at = $at ?? now();
        return $q->where(function ($q) use ($at) {
            $q->whereNull('due_at')->orWhere('due_at', '<=', $at);
        });
    }
}
