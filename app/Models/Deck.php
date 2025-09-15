<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','parent_id','name','description'];

    public function user(){ return $this->belongsTo(User::class); }
    public function parent(){ return $this->belongsTo(Deck::class,'parent_id'); }
    public function children(){ return $this->hasMany(Deck::class,'parent_id'); }
    public function items(){ return $this->hasMany(Item::class); }
}
