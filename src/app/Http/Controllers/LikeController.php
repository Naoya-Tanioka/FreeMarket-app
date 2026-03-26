<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function toggle($item_id)
    {
        $userId = auth()->id();

        $exists = DB::table('likes')
            ->where('user_id', $userId)
            ->where('item_id', $item_id)
            ->exists();

        if ($exists) {
            DB::table('likes')
                ->where('user_id', $userId)
                ->where('item_id', $item_id)
                ->delete();
        } else {
            DB::table('likes')->insert([
                'user_id' => $userId,
                'item_id' => $item_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('items.detail', ['item_id' => $item_id]);
    }
}