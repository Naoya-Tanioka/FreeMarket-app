@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/mypage.css') }}">
@endsection

@section('content')
<div class="mypage">
    <div class="mypage__header">
        <div class="mypage__profile">
            <div class="mypage__image">
                @if(!empty($profile?->image))
                    <img src="{{ asset($profile->image) }}" alt="プロフィール画像">
                @endif
            </div>

            <h1 class="mypage__name">
                {{ $profile->name ?? $user->name }}
            </h1>

            <a href="{{ route('profile.edit') }}" class="mypage__edit-button">
                プロフィールを編集
            </a>
        </div>
    </div>

    <div class="mypage__tabs">
        <a
            href="{{ route('profile.show', ['tab' => 'sell']) }}"
            class="mypage__tab {{ $tab === 'sell' ? 'mypage__tab--active' : '' }}"
        >
            出品した商品
        </a>

        <a
            href="{{ route('profile.show', ['tab' => 'buy']) }}"
            class="mypage__tab {{ $tab === 'buy' ? 'mypage__tab--active' : '' }}"
        >
            購入した商品
        </a>
    </div>

    <div class="mypage__items">
        @forelse($items as $item)
            <a href="{{ route('items.detail', ['item_id' => $item->id]) }}" class="mypage__item-card">
                <div class="mypage__item-image">
                    <img src="{{ asset($item->image) }}" alt="{{ $item->name }}">
                </div>
                <p class="mypage__item-name">{{ $item->name }}</p>
            </a>
        @empty
            <p class="mypage__empty">商品がありません</p>
        @endforelse
    </div>
</div>
@endsection