<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // GET /orders: Lấy danh sách tất cả đơn hàng
    public function index()
    {
        return response()->json(Order::with('items')->get());
    }

    // GET /orders/{id}: Lấy chi tiết 1 đơn hàng
    public function show($id)
    {
        $order = Order::with('items')->find($id);
        if (!$order) return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        return response()->json($order);
    }

    // POST /orders: Tạo đơn hàng mới
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'items' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $orderItems = [];

            // GỌI SANG PRODUCT SERVICE (Bài 3) ĐỂ CHECK TỒN KHO
            // Lưu ý: Thay port 8001 bằng port thực tế của service Product nếu khác
            foreach ($request->items as $item) {
                $productResponse = Http::get("http://localhost:8001/api/products/{$item['product_id']}");
                
                if ($productResponse->failed()) {
                    throw new \Exception("Sản phẩm ID {$item['product_id']} không tồn tại.");
                }

                $product = $productResponse->json();

               if ($product['quantity'] < $item['quantity']) {
    throw new \Exception("Sản phẩm '{$product['name']}' không đủ hàng.");
}

                $totalPrice = $product['price'] * $item['quantity'];
                $totalAmount += $totalPrice;

                $orderItems[] = [
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product['price'],
                    'total_price' => $totalPrice,
                ];
            }

            // Lưu vào bảng orders
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            // Lưu vào bảng order_items
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
            }

            DB::commit();
            return response()->json($order->load('items'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // PUT /orders/{id}: Cập nhật trạng thái
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) return response()->json(['message' => 'Không tìm thấy'], 404);

        $order->update(['status' => $request->status]);
        return response()->json($order);
    }

    // DELETE /orders/{id}: Xóa đơn hàng
    public function destroy($id)
    {
        Order::destroy($id);
        return response()->json(['message' => 'Đã xóa thành công']);
    }
}