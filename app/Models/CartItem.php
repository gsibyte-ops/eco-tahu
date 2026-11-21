<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'user_id',
        'item_type',
        'item_id',
        'qty',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
