<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;

class FoodController extends Controller
{
    public function index()
    {
        return Food::all();
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|string'
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Simpan gambar ke folder 'public/foods'
            $imagePath = $request->file('image')->store('foods', 'public');
        }

        $food = Food::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $imagePath ? url('storage/' . $imagePath) : null,
            'description' => $request->description ?? '-'
        ]);
        return response()->json(['message' => 'Menu berhasil ditambah', 'data' => $food], 201);
    }

    public function update(Request $request, Food $food)
    {
        $food->update($request->all());
        return $food;
    }

    public function destroy(Food $food)
    {
        $food = Food::findOrFail($food->id);
        $food->delete();
        return response()->json(['message' => 'Menu berhasil dihapus']);
    }
}
