<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'brand_name',
        'image',
        'condition',
        'description',
        'price',
        'status',
    ];
    public function order()
    {
        return $this->hasOne(Order::class, 'item_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'item_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
