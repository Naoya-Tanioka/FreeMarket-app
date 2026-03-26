<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 出品者（ダミーユーザー）
        $userId = DB::table('users')->first()->id;

        // condition 変換表
        $conditionMap = [
            '良好' => 1,
            '目立った傷や汚れなし' => 2,
            'やや傷や汚れあり' => 3,
            '状態が悪い' => 4,
        ];

        // カテゴリID取得
        $categories = DB::table('categories')->pluck('id', 'name');

        $seedImageDir = database_path('seeders/item_images');

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand_name' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'watch.jpg',
                'condition_text' => '良好',
                'categories' => ['ファッション', 'メンズ', 'アクセサリー'],
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand_name' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'hdd.jpg',
                'condition_text' => '目立った傷や汚れなし',
                'categories' => ['家電'],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand_name' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'onion.jpg',
                'condition_text' => 'やや傷や汚れあり',
                'categories' => ['キッチン'],
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand_name' => null,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'shoes.jpg',
                'condition_text' => '状態が悪い',
                'categories' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand_name' => null,
                'description' => '高性能なノートパソコン',
                'image' => 'notePC.jpg',
                'condition_text' => '良好',
                'categories' => ['家電'],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand_name' => null,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'mic.jpg',
                'condition_text' => '目立った傷や汚れなし',
                'categories' => ['家電'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand_name' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'bag.jpg',
                'condition_text' => 'やや傷や汚れあり',
                'categories' => ['ファッション', 'レディース'],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand_name' => null,
                'description' => '使いやすいタンブラー',
                'image' => 'tumbler.jpg',
                'condition_text' => '状態が悪い',
                'categories' => ['キッチン'],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand_name' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image' => 'mill.jpg',
                'condition_text' => '良好',
                'categories' => ['キッチン'],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand_name' => null,
                'description' => '便利なメイクアップセット',
                'image' => 'makeset.jpg',
                'condition_text' => '目立った傷や汚れなし',
                'categories' => ['コスメ'],
            ],
        ];

        foreach ($items as $item) {
             // 1) 画像を storage/app/public/items にコピー
            $source = $seedImageDir . DIRECTORY_SEPARATOR . $item['image'];

            // 保存ファイル名（衝突回避）
            $ext = pathinfo($item['image'], PATHINFO_EXTENSION);
            $storedName = Str::uuid() . '.' . $ext;

            // publicディスク = storage/app/public
            Storage::disk('public')->put('items/' . $storedName, file_get_contents($source));

            // 2) DBに保存するパス（ブラウザから見えるのは /storage/...）
            $publicPath = 'storage/items/' . $storedName;
            $itemId = DB::table('items')->insertGetId([
                'user_id' => $userId,
                'name' => $item['name'],
                'brand_name' => $item['brand_name'],
                'image' => $publicPath,
                'condition' => $conditionMap[$item['condition_text']],
                'description' => $item['description'],
                'price' => $item['price'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // category_item 登録
            foreach ($item['categories'] as $categoryName) {
                DB::table('category_item')->insert([
                    'item_id' => $itemId,
                    'category_id' => $categories[$categoryName],
                ]);
                }
            }
    }
}
