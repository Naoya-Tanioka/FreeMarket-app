@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-page">
    <div class="verify-page__inner">
        <p class="verify-page__text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        <a href="{{ config('app.mail_service_url', '#') }}" class="verify-page__button">
            認証はこちらから
        </a>

        <form action="{{ route('verification.send') }}" method="post" class="verify-page__resend-form">
            @csrf
            <button type="submit" class="verify-page__resend-button">
                認証メールを再送する
            </button>
        </form>

        @if (session('message'))
            <p class="verify-page__message">{{ session('message') }}</p>
        @endif
    </div>
</div>
@endsection