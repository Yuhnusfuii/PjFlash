<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
        'slug',
    ];

    protected $casts = [
        'is_public' => 'bool',
    ];

    /* ---------------- Relationships ---------------- */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /* ---------------- Scopes ---------------- */

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOwned($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /* ---------------- Slug lifecycle ---------------- */

    protected static function booted(): void
    {
        // Gán slug duy nhất khi TẠO mới
        static::creating(function (Deck $deck) {
            if (blank($deck->slug)) {
                $deck->slug = static::makeUniqueSlug($deck->name);
            }
        });

        // Giữ slug ổn định để link không đổi.
        // Nếu muốn đổi slug khi đổi name, bật đoạn dưới và nhớ xử lý redirect.
        /*
        static::updating(function (Deck $deck) {
            if ($deck->isDirty('name') && blank($deck->slug)) {
                $deck->slug = static::makeUniqueSlug($deck->name);
            }
        });
        */
    }

    /**
     * Tạo slug duy nhất từ name.
     */
    public static function makeUniqueSlug(?string $name): string
    {
        $base = Str::slug($name ?? '') ?: 'deck';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
