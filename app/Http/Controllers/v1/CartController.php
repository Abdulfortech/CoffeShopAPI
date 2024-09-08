<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    //
    public function index(Request $request)
    {
        $carts = Cart::where('status', 'Active')->where('userID', $request->user()->id)->orderBy('id', 'desc')->get();

        if (!$carts) {
            return response()->json(['status'=>false, 'message' => 'No item found in the cart'], 404);
        }

        return response()->json(['status'=>true, 'data' => $carts], 200);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coffeeID' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'total' => 'required|numeric',
            'status' => 'required|string'
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }

        $cart = new Cart([
            'userID' => $request->user()->id,
            'coffeeID' => $request->coffeeID,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'total' => $request->total,
            'status' => $request->status,
        ]);

        $cart->save();
        return response()->json(['status'=>true, 'message' => 'Product has been added to Cart', 'data'=> $cart], 200);
        
    }

    
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'quantity' => 'required|integer',
            'total' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }

        $carts = Cart::where('status', 'Active')->where('userID', $request->user()->id)->get();

        if ($carts->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Cart items not found'], 404);
        }
        
        $status = ($request->payment_method && $request->payment_status == "Paid") ? 'Active' : 'Pending';
        

        $order = Order::create([
            'userID' => $request->user()->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'quantity' => $carts->count(),
            'total' => $request->total,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'status' => $status
        ]);

        // update the cart items with the created order_id
        foreach ($carts as $cart) {
            $cart->update([
                'orderID' => $order->id,
                'status' => 'Processed'
            ]);
        }
        

        return response()->json(['status' => true, "message" => "Order has been created", 'data'=> $order]);
    }

    
    public function remove(Request $request, $id)
    {
        $cart = Cart::find($id);

        if (!$cart) {
            return response()->json(['status'=>false, 'message' => 'Cart not found'], 404);
        }

        $cart->delete();

        return response()->json(['status' => true, 'message' => 'Cart item has been removed successfully']);
    }
}
