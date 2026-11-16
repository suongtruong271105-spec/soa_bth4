<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'product_name', 'quantity', 'unit_price', 'total_price'];

    // Chi tiết này thuộc về 1 Đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}