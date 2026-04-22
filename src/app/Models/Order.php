<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    // もし create/insert で使うなら fillable を追加
    protected $fillable = [
        'item_id',
        'buyer_id',
        'payment_method',
        'ship_post_code',
        'ship_address',
        'ship_building',
        'status',
    ];
    use HasFactory;

        public function item()
    {
        return $this->belongsTo(Item::class);
    }

        public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
