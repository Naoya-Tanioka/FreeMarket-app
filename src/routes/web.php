<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//商品一覧ページへの遷移
Route::get('/', [ItemController::class, 'index'])->name('items.index');
//商品詳細ページへの遷移
Route::get('/item/{item_id}', [ItemController::class, 'detail'])
    ->name('items.detail');
//ログイン後のみ利用可能
Route::middleware('auth')->group(function (){
    //マイページへの遷移
    Route::get('/mypage',[ProfileController::class, 'show'])->name('profile.show');
    //プロフィール設定画面
    Route::get('/mypage/profile',[ProfileController::class, 'edit'])->name('profile.edit');
    //プロフィール更新
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    //商品出品画面
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    //商品出品画面(登録)
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
    //いいね
    Route::post('/item/{item_id}/like', [LikeController::class, 'toggle'])->name('items.like.toggle');
    //コメント投稿
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('items.comment.store');
    //商品購入画面
    Route::get('purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    // 送付先住所変更画面
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    // 住所変更ページ
    Route::put('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    // 購入確定
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
});

