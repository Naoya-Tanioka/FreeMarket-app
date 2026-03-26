<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'condition' => 'required|integer',
            'name' => 'required|string|max:100',
            'brand_name' => 'nullable|string|max:100',
            'description' => 'required|string',
            'price' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => '商品画像を選択してください',
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください',
            'categories.required' => 'カテゴリーを1つ以上選択してください',
            'condition.required' => '商品の状態を選択してください',
            'name.required' => '商品名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '販売価格は数字で入力してください',
            'price.min' => '販売価格は1円以上で入力してください',
        ];
    }
}