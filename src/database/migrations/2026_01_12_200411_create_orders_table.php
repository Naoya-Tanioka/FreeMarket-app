<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 1商品につき1注文（売れたら1件）
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();

            // 支払い方法(2種): 例 1=クレカ,2=コンビニ など
            $table->unsignedTinyInteger('payment_method');

            // 配送先スナップショット（注文時に確定）
            $table->string('shipping_post_code', 8);
            $table->string('shipping_address', 255);
            $table->string('shipping_building', 255)->nullable();

            // 注文状態: 例 1=支払待ち,2=発送待ち,3=発送済み,4=完了,5=キャンセル
            $table->unsignedTinyInteger('status')->default(1);

            $table->timestamps();

            $table->unique('item_id'); // 1商品1注文をDBで担保
            $table->index(['buyer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
