@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/exhibit.css') }}">
@endsection

@section('content')
<div class="sell-page">
    <div class="sell-page__inner">
        <h1 class="sell-page__title">商品の出品</h1>

        <form action="{{ route('sell.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="section">
                <label class="form-label">商品画像</label>
                <div class="image-upload">
                    <div class="image-preview">
                        <img id="imagePreview" src="" alt="画像プレビュー" style="display: none;">
                        <input type="file" name="image" id="image" class="image-upload__input">
                        <label id="uploadButton" for="image" class="image-upload__button">
                            画像を選択する
                        </label>
                    </div>
                </div>
                @error('image')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="section">
                <h2 class="section__title">商品の詳細</h2>

                <div class="form-group">
                    <label class="form-label">カテゴリー</label>
                    <div class="category-list">
                        @foreach ($categories as $category)
                            <label class="category-pill">
                                <input type="checkbox"name="categories[]"value="{{ $category->id }}"{{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('categories')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="condition">商品の状態</label>
                    <select name="condition" id="condition" class="form-select">
                        <option value="">選択してください</option>
                        <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                        <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                        <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                        <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
                    </select>
                    @error('condition')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="section">
                <h2 class="section__title">商品名と説明</h2>

                <div class="form-group">
                    <label class="form-label" for="name">商品名</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}">
                    @error('name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="brand_name">ブランド名</label>
                    <input type="text" name="brand_name" id="brand_name" class="form-input" value="{{ old('brand_name') }}">
                    @error('brand_name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">商品の説明</label>
                    <textarea name="description" id="description" class="form-textarea">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="price">販売価格</label>
                    <div class="price-box">
                        <span class="price-box__yen">¥</span>
                        <input type="text" name="price" id="price" class="form-input form-input--price" value="{{ old('price') }}">
                    </div>
                    @error('price')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="submit-area">
                <button type="submit" class="submit-button">出品する</button>
            </div>
        </form>
    </div>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const button = document.getElementById('uploadButton');

    input.addEventListener('change', function(e) {

        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();

        reader.onload = function(event) {

            preview.src = event.target.result;
            preview.style.display = "block";

            // ボタンを消す
            button.style.display = "none";

        };

        reader.readAsDataURL(file);

    });

});
</script>