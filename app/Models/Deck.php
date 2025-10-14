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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Chủ sở hữu deck
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Flashcards/items trong deck
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    // Chỉ deck public
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Deck thuộc về một user id
    public function scopeOwned($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /*
    |--------------------------------------------------------------------------
    | Slug (unique) khi tạo/cập nhật tên
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::creating(function (Deck $deck) {
            // nếu đã có slug thì giữ nguyên, ngược lại tạo theo name
            if (empty($deck->slug)) {
                $deck->slug = static::uniqueSlug($deck->name);
            }
        });

        static::updating(function (Deck $deck) {
            if ($deck->isDirty('name')) {
                // chỉ đổi slug khi name đổi (nếu bạn không muốn đổi slug, bỏ đoạn này)
                $deck->slug = static::uniqueSlug($deck->name, $deck->id);
            }
        });
    }

    protected static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'deck';
        $slug = $base;
        $i = 1;

        $exists = static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();

        while ($exists) {
            $slug = $base.'-'.$i++;
            $exists = static::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists();
        }

        return $slug;
    }
}
