<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Khai báo các cột được phép thêm dữ liệu
    protected $fillable = ['customer_name', 'customer_email', 'total_amount', 'status'];

    // 1 Đơn hàng có nhiều Chi tiết đơn hàng
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}