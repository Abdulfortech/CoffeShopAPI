<?php

namespace App\Http\Controllers\v1;

use App\Models\Coffee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CoffeeController extends Controller
{
    //
    public function index(Request $request)
    {
        $coffees = Coffee::whereNotNull('status')->orderBy('id', 'desc')->get();

        if (!$coffees) {
            return response()->json(['status'=>false, 'message' => 'Coffees not found'], 404);
        }

        return response()->json(['status'=>true, 'data' => $coffees], 200);
    }

    
    public function single(Request $request, $id)
    {
        $coffee = Coffee::find($id);

        if (!$coffee) {
            return response()->json(['status'=>false, 'message' => 'Coffee not found'], 404);
        }

        return response()->json(['status'=>true, "data" => $coffee]);
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'body' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|string'
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }

        $user = $request->user();

        // Check if the user is authenticated
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $coffee = new Coffee([
            'title' => $request->title,
            'body' => $request->body,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        $coffee->save();
        return response()->json(['status'=>true, 'message' => 'Coffee product has been added', 'data'=> $coffee], 200);
        
    }

    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'body' => 'required|string',
            'price' => 'required|numeric',
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }

        $coffee = Coffee::find($id);

        if (!$coffee) {
            return response()->json(['status'=>false, 'message' => 'Coffee not found', ], 404);
        }


        $coffee->update([
            'title' => $request->title,
            'body' => $request->body,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        return response()->json(['status' => true, "message" => "Coffee product has been updated", 'data'=> $coffee]);
    }

    
    public function delete(Request $request, $id)
    {
        $coffee = Coffee::find($id);

        if (!$coffee) {
            return response()->json(['status'=>false, 'message' => 'Coffee not found'], 404);
        }

        $coffee->delete();

        return response()->json(['status' => true, 'message' => 'Coffee product has been deleted successfully']);
    }
}
