<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    // GET /order_items: Lấy danh sách tất cả mặt hàng
    public function index()
    {
        return response()->json(OrderItem::all());
    }

    // GET /order_items/{id}: Lấy thông tin chi tiết một mặt hàng
    public function show($id)
    {
        $item = OrderItem::find($id);
        if (!$item) return response()->json(['message' => 'Không tìm thấy chi tiết đơn hàng'], 404);
        
        return response()->json($item);
    }

    // POST /order_items: Tạo mặt hàng mới
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|integer',
            'product_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric'
        ]);

        // Tự động tính tổng tiền của mặt hàng này
        $total_price = $request->quantity * $request->unit_price;

        $item = OrderItem::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'product_name' => $request->product_name,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_price' => $total_price
        ]);

        return response()->json($item, 201);
    }

    // PUT /order_items/{id}: Cập nhật mặt hàng (VD: Cập nhật số lượng)
    public function update(Request $request, $id)
    {
        $item = OrderItem::find($id);
        if (!$item) return response()->json(['message' => 'Không tìm thấy'], 404);

        $data = $request->only(['quantity', 'unit_price', 'product_name']);
        
        // Nếu có cập nhật số lượng hoặc đơn giá thì hệ thống tự tính lại tổng tiền
        if (isset($data['quantity']) || isset($data['unit_price'])) {
            $quantity = $data['quantity'] ?? $item->quantity;
            $unitPrice = $data['unit_price'] ?? $item->unit_price;
            $data['total_price'] = $quantity * $unitPrice;
        }

        $item->update($data);
        
        return response()->json($item);
    }

    // DELETE /order_items/{id}: Xóa một mặt hàng trong đơn hàng
    public function destroy($id)
    {
        OrderItem::destroy($id);
        return response()->json(['message' => 'Đã xóa mặt hàng thành công']);
    }
}