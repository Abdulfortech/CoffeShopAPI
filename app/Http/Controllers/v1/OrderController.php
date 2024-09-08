<?php

namespace App\Http\Controllers\v1;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    //

    public function index(Request $request)
    {
        $orders = Order::whereNotNull('status')->where('userID', $request->user()->id)->orderBy('id', 'desc')->get();

        if ($orders->isEmpty()) {
            return response()->json(['status'=>false, 'message' => 'Orders not found'], 404);
        }

        return response()->json(['status'=>true, 'data' => $orders], 200);
    }

    
    public function single(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['status'=>false, 'message' => 'order not found'], 404);
        }

        $newOrderResponse = [
            'status' => true,
            'data' => [
                'id' => $order->id,
                'name' => $order->name,
                'phone' => $order->phone,
                'address' => $order->address,
                'quantity' => $order->quantity,
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'cart_items' => $order->carts->map(function($cart) {
                    return [
                        'id' => $cart->id,
                        'product_name' => $cart->product ? $cart->product->title : 'Unknown Product',
                        'quantity' => $cart->quantity,
                        'price' => $cart->price
                    ];
                }),
                'payment' => $order->payment ? [
                    'id' => $order->payment->id,
                    'amount' => $order->payment->amount,
                    'gateway' => $order->payment->gateway,
                    'reference' => $order->payment->reference,
                    'payment_date' => $order->payment->payment_date,
                    'payment_method' => $order->payment->payment_method,
                    'status' => $order->payment->status,
                    'created_at' => $order->payment->created_at
                ] : null
            ]
        ];
        

        return response()->json($newOrderResponse);
    }

    
    public function all(Request $request)
    {
        $user = $request->user(); 

        // Check if the user is admin
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $orders = Order::whereNotNull('status')->orderBy('id', 'desc')->get();

        if ($orders->isEmpty()) {
            return response()->json(['status'=>false, 'message' => 'Orders not found'], 404);
        }

        return response()->json(['status'=>true, 'data' => $orders], 200);
    }

    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }

        // check if th order exisit
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status'=>false, 'message' => 'order not found', ], 404);
        }

        // Check if the user role is admin
        $user = $request->user(); 
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }


        $order->update([
            'status' => $request->status,
        ]);

        return response()->json(['status' => true, "message" => "order has been updated", 'data'=> $order]);
    }

}
