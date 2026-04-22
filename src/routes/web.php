<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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
Route::get('/item/{item_id}', [ItemController::class, 'detail'])->name('items.detail');
//メール認証前
Route::middleware('auth')->group(function(){
    //メール認証誘導画面への遷移
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    // 認証メール再送
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
    //メール内リンクからのアクセス
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('profile.edit');
    })->middleware(['signed'])->name('verification.verify');
});
//ログイン後のみ利用可能
Route::middleware(['auth','verified'])->group(function (){
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
    // Stripe Checkout Session 作成
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
    // 決済完了後の遷移先
    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');
    // キャンセル時の戻り先
    Route::get('/purchase/cancel/{item_id}', [PurchaseController::class, 'cancel'])->name('purchase.cancel');
});

// Webhook は auth なし
Route::post('/stripe/webhook', [PurchaseController::class, 'webhook'])->name('stripe.webhook');

