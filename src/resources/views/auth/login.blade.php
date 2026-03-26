@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-page">
    <h1 class="title">ログイン</h1>

    <form class="form" action="/login" method="post">
        @csrf

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

        <div class="actions">
            <button class="btn" type="submit">ログインする</button>
            <a class="link" href="/register">会員登録はこちら</a>
        </div>
    </form>
</div>
@endsection