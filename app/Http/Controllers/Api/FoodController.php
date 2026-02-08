<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;

class FoodController extends Controller
{
    public function index() { return Food::all(); }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'image' => 'nullable|string' // Sederhanakan dulu dengan string URL/base64
        ]);
        return Food::create($data);
    }

    public function update(Request $request, Food $food)
    {
        $food->update($request->all());
        return $food;
    }

    public function destroy(Food $food)
    {
        $food->delete();
        return response()->json(['message' => 'Deleted']);
    }

}
