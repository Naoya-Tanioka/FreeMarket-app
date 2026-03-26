@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/profile.css')}}">
@endsection

@section('content')
<div class="profile-page">
    <h1 class="title">プロフィール設定</h1>
    <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data" class="form">
                @csrf
                @method('PUT')
{{-- プロフィール画像 --}}
    <div class="form__group profile-image-group">
        <div class="profile-image-area">
            <div class="profile-image-preview">
                <img id="profileImagePreview"src="{{ !empty($profile?->image) ? asset($profile->image) : '' }}"alt="プロフィール画像プレビュー"style="{{ !empty($profile?->image) ? 'display:block;' : 'display:none;' }}"
    >
            </div>

            <input
                type="file"
                name="image"
                id="image"
                class="profile-image-input"
                accept="image/jpeg,image/png,image/jpg"
            >

            <label for="image" class="profile-image-button">画像を選択する</label>
        </div>
    </div>
        {{-- ユーザー名 --}}
        <div class="form__group">
            <label class="label">ユーザー名</label>
            <input
                class="input"
                type="text"
                name="name"
                value="{{ old('name', $profile->name ?? '')}}"
            >
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 郵便番号 --}}
        <div class="form__group">
            <label class="label">郵便番号</label>
            <input
                class="input"
                type="text"
                name="post_code"
                value="{{ old('post_code', $profile->post_code ?? '') }}"
            >
            @error('post_code')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="form__group">
            <label class="label">住所</label>
            <input
                class="input"
                type="text"
                name="address"
                value="{{ old('address', $profile->address ?? '') }}"
            >
            @error('address')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名（任意） --}}
        <div class="form__group">
            <label class="label">建物名</label>
            <input
                class="input"
                type="text"
                name="building"
                value="{{ old('building', $profile->building ?? '') }}"
            >
            @error('building')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="actions">
            <button class="update__btn">更新する</button>
        </div>
    </form>
</div>


@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('image');
    const preview = document.getElementById('profileImagePreview');

    input.addEventListener('change', function (e) {
        const file = e.target.files[0];

        if (!file) {
            preview.src = '';
            preview.style.display = 'none';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            preview.src = event.target.result;
            preview.style.display = 'block';
        };

        reader.readAsDataURL(file);
    });
});
</script>