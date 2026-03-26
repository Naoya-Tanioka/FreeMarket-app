<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    public function create()
    {
        $categories = DB::table('categories')->get();

        return view('exhibit', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        DB::transaction(function () use ($request) {
            // 1. 画像を storage/app/public/items に保存
            $path = $request->file('image')->store('items', 'public');
            $imagePath = 'storage/' . $path;

            // 2. items テーブルに保存
            $itemId = DB::table('items')->insertGetId([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'brand_name' => $request->brand_name,
                'image' => $imagePath,
                'condition' => $request->condition,
                'description' => $request->description,
                'price' => $request->price,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. category_item テーブルに保存
            foreach ($request->categories as $categoryId) {
                DB::table('category_item')->insert([
                    'item_id' => $itemId,
                    'category_id' => $categoryId,
                ]);
            }
        });

        return redirect()->route('items.index');
    }
}