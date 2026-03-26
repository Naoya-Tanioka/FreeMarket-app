@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-page">
    <h1 class="title">会員登録</h1>

    <form class="form" action="/register" method="post">
        @csrf

        <div class="form__group">
            <label class="label" for="name">ユーザー名</label>
            <input
                class="input @error('name') input--error @enderror"
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
            />
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label class="label" for="email">メールアドレス</label>
            <input
                class="input @error('email') input--error @enderror"
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
            />
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label class="label" for="password">パスワード</label>
            <input
                class="input @error('password') input--error @enderror"
                type="password"
                id="password"
                name="password"
            />
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label class="label" for="password_confirmation">確認用パスワード</label>
            <input
                class="input @error('password_confirmation') input--error @enderror"
                type="password"
                id="password_confirmation"
                name="password_confirmation"
            />
            @error('password_confirmation')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="actions">
            <button class="btn" type="submit">登録する</button>
            <a class="link" href="/login">ログインはこちら</a>
        </div>
    </form>
</div>
@endsection
