<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name',100);
            $table->string('brand_name',100)->nullable();
            $table->string('image',255);
            // 状態(4種): 例 1=新品,2=未使用に近い,3=目立った傷なし,4=傷あり など
            $table->unsignedTinyInteger('condition');
            $table->text('description');
            $table->unsignedInteger('price');
            // 出品状態: 例 1=出品中,2=取引中,3=売却済
            $table->unsignedTinyInteger('status')->default(1);
            
            $table->index(['user_id','status']);
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
}
