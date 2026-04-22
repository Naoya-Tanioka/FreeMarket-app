<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $q   = $request->query('q');

        $query = Item::query();

        // 自分の出品は除外（ログイン時）
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }
        
         // 検索（商品名の部分一致）
        if (!empty($q)) {
            $query->where('items.name', 'like', '%' . $q . '%');
            }
        // Sold判定（ordersが存在するか）
        $query->withExists('order');
        // マイリスト
        if ($tab === 'mylist') {
            // 未認証は「何も表示されない」
            if (!Auth::check()) {
                $items = collect();
                return view('index', compact('items', 'tab', 'q'));
        }

            // いいねした商品だけ
            $query->whereHas('likes', function ($q) {
            $q->where('user_id', Auth::id());
        });
        }
        // 並び順（新しい順など）
        $items = $query->orderByDesc('items.created_at')->get();

        return view('index', compact('items', 'tab', 'q'));
    }

        public function detail($item_id)
    {
        $item = DB::table('items')
            ->where('id', $item_id)
            ->first();

        if (!$item) {
            abort(404);
        }

        $categories = DB::table('category_item')
            ->join('categories', 'category_item.category_id', '=', 'categories.id')
            ->where('category_item.item_id', $item_id)
            ->select('categories.name')
            ->get();

        $likesCount = DB::table('likes')
            ->where('item_id', $item_id)
            ->count();

        $comments = DB::table('comments')
            ->leftJoin('profiles', 'comments.user_id', '=', 'profiles.user_id')
            ->where('comments.item_id', $item_id)
            ->select(
                'comments.*',
                'profiles.name as profile_name',
                'profiles.image as profile_image'
            )
            ->orderBy('comments.created_at', 'asc')
            ->get();

        $commentsCount = $comments->count();

        $isLiked = false;

        if (auth()->check()) {
            $isLiked = DB::table('likes')
                ->where('user_id', auth()->id())
                ->where('item_id', $item_id)
                ->exists();
        }

        return view('detail', compact(
            'item',
            'categories',
            'likesCount',
            'comments',
            'commentsCount',
            'isLiked'
        ));
    }

    public function purchase($item_id)
    {
        // ここは購入画面作成時に中身を実装
        return view('purchase', compact('item_id'));
    }
}