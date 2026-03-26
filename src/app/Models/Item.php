<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function order()
    {
        return $this->hasOne(Order::class, 'item_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'item_id');
    }
}
