<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Like;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;


class HelloTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_top_page_can_be_displayed()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    //会員登録画面テストケース
    //名前未入力
    public function test_name_is_required_for_register()
    {
        $response = $this->from('/register')->followingRedirects()->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSee('お名前を入力してください');
    }
    //メール未入力
    public function test_email_is_required_for_register()
    {
        $response = $this->from('/register')->followingRedirects()->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSee('メールアドレスを入力してください');
    }
    //パスワード未入力
    public function test_password_is_required_for_register()
    {
        $response = $this->from('/register')->followingRedirects()->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSee('パスワードを入力してください');
    }
    //パスワード7文字以下
    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->from('/register')->followingRedirects()->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSee('パスワードは8文字以上で入力してください');
    }
    //パスワード不一致
    public function test_password_confirmation_must_match()
    {
        $response = $this->from('/register')->followingRedirects()->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertSee('パスワードと一致しません');
    }
    //正しく入力
    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
    //ログイン済みユーザーのみ閲覧可能のテストがコメント以外必要ないようであれば以下を削除する//
    public function test_guest_cannot_access_mypage()
    {
        $response = $this->get('/mypage');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_profile()
    {
        $response = $this->get('/mypage/profile');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_sell()
    {
        $response = $this->get('/sell');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_purchase()
    {
        $response = $this->get('/purchase/{item_id}');

        $response->assertRedirect('/login');
    }
    //::ログイン済みユーザーここまで削除::://
    
    //ログイン機能テスト
    //メールアドレス未入力
    public function test_email_is_required_for_login()
    {
        $response = $this->from('/login')->followingRedirects()->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSee('メールアドレスを入力してください');
    }
    //パスワード未入力
    public function test_password_validation_message_is_displayed_on_login()
    {
        $response = $this->from('/login')->followingRedirects()->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSee('パスワードを入力してください');
    }
    //登録情報と違う
    public function test_login_error_message_is_displayed_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->from('/login')->followingRedirects()->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSee('ログイン情報が登録されていません');
    }
    //正しく入力
    public function test_user_can_login_with_correct_credentials()
{
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
}
    //ログアウト処理
    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertStatus(302);

        $this->assertGuest();
    }

    //商品一覧表示テスト
    public function test_all_items_are_displayed_on_index_page()
    {
        $item1 = Item::factory()->create([
            'name' => '商品A',
        ]);

        $item2 = Item::factory()->create([
            'name' => '商品B',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('商品A');
        $response->assertSee('商品B');
    }

    //Sold表示
    public function test_sold_label_is_displayed_for_purchased_item()
    {
        $item = Item::factory()->create([
            'name' => '購入済み商品',
            'status' => 3,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('Sold');
    }

    //自分の出品した商品は表示されない
    public function test_items_list_does_not_display_items_created_by_authenticated_user()
    {
        $user = User::factory()->create();

        $myItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);

        $otherItem = Item::factory()->create([
            'name' => '他人の商品',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }
    //マイリスト表示のテスト
    //いいねの商品だけ表示
        public function test_only_liked_items_are_displayed_in_mylist()
    {
        $user = User::factory()->create();

        $likedItem = Item::factory()->create([
            'name' => 'いいねした商品',
        ]);

        $notLikedItem = Item::factory()->create([
            'name' => 'いいねしていない商品',
        ]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));


        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしていない商品');
    }
    //sold表示
    public function test_sold_label_is_displayed_for_purchased_item_in_mylist()
    {
        $user = User::factory()->create();

        $soldItem = Item::factory()->create([
            'name' => '購入済み商品',
            'status' => 3,
        ]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $soldItem->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('Sold');
    }
    //未ログインなら何も表示されない
    public function test_guest_sees_nothing_in_mylist()
    {
        $item = Item::factory()->create([
            'name' => 'いいね商品',
            'status' => 1,
        ]);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);
        $response->assertDontSee('いいね商品');
    }
    //商品検索機能
    //部分一致検索
    public function test_items_can_be_searched_by_partial_name_match()
    {
        Item::factory()->create([
            'name' => '赤いバッグ',
        ]);

        Item::factory()->create([
            'name' => '青いバッグ',
        ]);

        Item::factory()->create([
            'name' => '腕時計',
        ]);

        $response = $this->get(route('items.index', [
            'tab' => 'recommend',
            'q' => 'バッグ',
        ]));

        $response->assertStatus(200);
        $response->assertSee('赤いバッグ');
        $response->assertSee('青いバッグ');
        $response->assertDontSee('腕時計');
    }

    //マイリストでの検索状態保持
    public function test_search_keyword_is_kept_when_switching_to_mylist_tab()
    {
        $user = User::factory()->create();

        $likedAndMatchedItem = Item::factory()->create([
            'name' => '赤いバッグ',
            'status' => 1,
        ]);

        $likedButNotMatchedItem = Item::factory()->create([
            'name' => '青い財布',
            'status' => 1,
        ]);

        $notLikedButMatchedItem = Item::factory()->create([
            'name' => '黒いバッグ',
            'status' => 1,
        ]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedAndMatchedItem->id,
        ]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedButNotMatchedItem->id,
        ]);

        $response = $this->actingAs($user)->get(route('items.index', [
            'tab' => 'mylist',
            'q' => 'バッグ',
        ]));

        $response->assertStatus(200);
        $response->assertSee('赤いバッグ');
        $response->assertDontSee('青い財布');
        $response->assertDontSee('黒いバッグ');
    }

    public function test_item_detail_page_displays_required_information()
    {
        $seller = User::factory()->create([
            'name' => '出品者ユーザー',
        ]);

        $commentUser = User::factory()->create([
            'name' => 'コメントユーザー',
        ]);
        Profile::factory()->create([
            'user_id' => $commentUser->id,
            'name' =>'コメントユーザー',
        ]);

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'image' => 'storage/items/test.jpg',
            'price' => 5000,
            'description' => 'これはテスト商品の説明です',
            'condition' => 1,
        ]);

        $category = Category::factory()->create([
            'name' => 'ファッション',
        ]);

        $item->categories()->attach($category->id);

        Like::factory()->count(2)->create([
            'item_id' => $item->id,
        ]);

        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'body' => 'とても良い商品です',
        ]);

        $response = $this->get(route('items.detail', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('5,000');
        $response->assertSee('これはテスト商品の説明です');

        $response->assertSee('ファッション');
        $response->assertSee('コメントユーザー');
        $response->assertSee('とても良い商品です');

        $response->assertSee('2');
        $response->assertSee('1');

        $response->assertSee('storage/items/test.jpg');
    }
    public function test_item_detail_page_displays_multiple_categories()
    {
        $item = Item::factory()->create([
            'name' => 'カテゴリ確認商品',
        ]);

        $category1 = Category::factory()->create([
            'name' => '家電',
        ]);

        $category2 = Category::factory()->create([
            'name' => 'ゲーム',
        ]);

        $category3 = Category::factory()->create([
            'name' => 'インテリア',
        ]);

        $item->categories()->attach([
            $category1->id,
            $category2->id,
            $category3->id,
        ]);

        $response = $this->get(route('items.detail', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('家電');
        $response->assertSee('ゲーム');
        $response->assertSee('インテリア');
    }
        public function test_user_can_unlike_an_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->post(route('items.like.toggle', [
            'item_id' => $item->id,
        ]));

        $response->assertStatus(302);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertEquals(0, Like::where('item_id', $item->id)->count());
    }

        public function test_authenticated_user_can_store_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('items.comment.store', [
            'item_id' => $item->id,
        ]), [
            'comment' => 'これはテストコメントです',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'body' => 'これはテストコメントです',
        ]);

        $this->assertEquals(1, Comment::where('item_id', $item->id)->count());
    }

    public function test_guest_cannot_store_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('items.comment.store', [
            'item_id' => $item->id,
        ]), [
            'comment' => 'ゲストのコメント',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'body' => 'ゲストのコメント',
        ]);
    }

    public function test_comment_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->from(route('items.detail', [
            'item_id' => $item->id,
        ]))->post(route('items.comment.store', [
            'item_id' => $item->id,
        ]), [
            'comment' => '',
        ]);

        $response->assertRedirect(route('items.detail', [
            'item_id' => $item->id,
        ]));

        $response->assertSessionHasErrors(['comment']);
    }

    public function test_comment_must_not_exceed_255_characters()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)->from(route('items.detail', [
            'item_id' => $item->id,
        ]))->post(route('items.comment.store', [
            'item_id' => $item->id,
        ]), [
            'comment' => $longComment,
        ]);

        $response->assertRedirect(route('items.detail', [
            'item_id' => $item->id,
        ]));

        $response->assertSessionHasErrors(['comment']);
    }

    public function test_authenticated_user_can_purchase_item()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($buyer)->post(route('purchase.store', [
            'item_id' => $item->id,
        ]), [
            'payment_method' => 'card',
        ]);

        $response->assertStatus(302);
    }

    public function test_user_is_redirected_to_profile_buy_tab_after_stripe_success()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($buyer)->get(route('purchase.success', [
            'item_id' => $item->id,
        ]));

        $response->assertRedirect(route('profile.show', ['tab' => 'buy']));
    }
    public function test_completed_purchase_item_is_displayed_as_sold()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '購入済み商品',
            'status' => 3,
        ]);

        Order::factory()->create([
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 1,
        ]);

        $response = $this->get(route('items.index', [
            'tab' => 'recommend',
        ]));

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('Sold');
    }
    public function test_completed_purchase_item_is_displayed_in_profile_buy_tab()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $purchasedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '購入した商品',
            'status' => 3,
        ]);

        $notPurchasedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '未購入商品',
            'status' => 1,
        ]);

        Order::factory()->create([
            'item_id' => $purchasedItem->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 1,
        ]);

        $response = $this->actingAs($buyer)->get(route('profile.show', [
            'tab' => 'buy',
        ]));

        $response->assertStatus(200);
        $response->assertSee('購入した商品');
        $response->assertDontSee('未購入商品');
    }

    public function test_updated_shipping_address_is_reflected_on_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->put(route('purchase.address.update', [
            'item_id' => $item->id,
        ]), [
            'post_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
        ]);

        $response->assertRedirect(route('purchase.show', [
            'item_id' => $item->id,
        ]));

        $purchaseResponse = $this->actingAs($user)->get(route('purchase.show', [
            'item_id' => $item->id,
        ]));

        $purchaseResponse->assertStatus(200);
        $purchaseResponse->assertSee('123-4567');
        $purchaseResponse->assertSee('東京都渋谷区1-1-1');
        $purchaseResponse->assertSee('テストビル');
    }
    public function test_profile_page_displays_user_information()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'name' => 'テストユーザー',
            'image' => 'storage/profiles/test.png',
            'post_code' => '123-4567',
            'address' => '東京都新宿区1-1-1',
            'building' => 'テストマンション',
        ]);

        $myItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品した商品',
            'status' => 1,
        ]);

        $seller = User::factory()->create();

        $purchasedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'name' => '購入した商品',
            'status' => 3,
        ]);

        Order::factory()->create([
            'item_id' => $purchasedItem->id,
            'buyer_id' => $user->id,
            'payment_method' => 1,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都新宿区1-1-1',
            'shipping_building' => 'テストマンション',
        ]);

        $response = $this->actingAs($user)->get(route('profile.show'));

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('出品した商品');

        $buyResponse = $this->actingAs($user)->get(route('profile.show', [
            'tab' => 'buy',
        ]));

        $buyResponse->assertStatus(200);
        $buyResponse->assertSee('購入した商品');
    }

    public function test_profile_edit_page_displays_initial_values()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
            'name' => 'テストユーザー',
            'image' => 'storage/profiles/test.png',
            'post_code' => '123-4567',
            'address' => '東京都新宿区1-1-1',
            'building' => 'テストマンション',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('value="テストユーザー"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="東京都新宿区1-1-1"', false);
        $response->assertSee('storage/profiles/test.png');
    }

        public function test_user_can_store_item_with_required_information()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $category1 = Category::factory()->create([
            'name' => 'ファッション',
        ]);

        $category2 = Category::factory()->create([
            'name' => 'メンズ',
        ]);

        $image = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post(route('sell.store'), [
            'image' => $image,
            'categories' => [$category1->id, $category2->id],
            'condition' => 1,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト用の商品説明です',
            'price' => 5000,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'condition' => 1,
            'description' => 'これはテスト用の商品説明です',
            'price' => 5000,
        ]);

        $item = Item::where('name', 'テスト商品')->first();

        $this->assertNotNull($item);

        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category2->id,
        ]);

        Storage::disk('public')->assertExists(str_replace('storage/', '', $item->image));
    }

        public function test_verification_email_is_sent_after_registration()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verification_notice_page_can_be_displayed()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
    }

    public function test_user_can_verify_email_and_is_redirected_to_profile_edit_page()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('profile.edit'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}


