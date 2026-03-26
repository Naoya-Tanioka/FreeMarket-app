<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function store(Request $request, $item_id)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ], [
            'comment.required' => 'コメントを入力してください',
        ]);

        DB::table('comments')->insert([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
            'body' => $request->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('items.detail', ['item_id' => $item_id]);
    }
}