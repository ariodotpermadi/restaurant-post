<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function openOrder(Request $request)
    {
        $request->validate(['table_id' => 'required|exists:tables,id']);

        return DB::transaction(function() use ($request) {
            $table = Table::findOrFail($request->table_id);
            if ($table->status !== 'available') return response()->json(['message' => 'Meja tidak tersedia'], 400);

            $order = Order::create([
                'table_id' => $table->id,
                'user_id' => auth()->id(),
                'status' => 'open'
            ]);

            $table->update(['status' => 'occupied']);
            return $order;
        });
    }

    public function addItem(Request $request, Order $order)
    {
        $request->validate([
            'food_id' => 'required|exists:foods,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric'
        ]);

        $item = $order->items()->create($request->all());

        // Update total harga order
        $order->increment('total_price', $request->price * $request->quantity);

        return $item;
    }

    public function closeOrder(Order $order)
    {
        return DB::transaction(function() use ($order) {
            $order->update(['status' => 'closed']);
            if ($order->table_id) {
                $table = Table::find($order->table_id);
                if ($table) {
                    $table->update(['status' => 'available']);
                }
            }
            return response()->json(['message' => 'Order closed successfully', 'total' => $order->total_price]);
        });
    }
}
