@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/detail.css') }}">
@endsection

@section('content')
<div class="detail-page">
    <div class="detail-page__inner">

        <div class="detail-page__image">
            <img src="{{ asset($item->image) }}" alt="{{ $item->name }}">
        </div>

        <div class="detail-page__content">
            <h1 class="detail-page__name">{{ $item->name }}</h1>

            <p class="detail-page__brand">{{ $item->brand_name }}</p>

            <p class="detail-page__price">
                <span class="detail-page__yen">¥</span>{{ number_format($item->price) }}
                <span class="detail-page__tax">（税込）</span>
            </p>

            <div class="detail-page__icons">
                <div class="detail-page__icon-group">
                    @auth
                        <form action="{{ route('items.like.toggle', ['item_id' => $item->id]) }}" method="post">
                            @csrf
                            <button type="submit" class="detail-page__icon-button">
                                <img
                                    src="{{ asset($isLiked ? 'storage/ハートロゴ_ピンク.png' : 'storage/ハートロゴ_デフォルト.png') }}"
                                    alt="いいね"
                                >
                            </button>
                        </form>
                    @endauth

                    @guest
                        <a href="/login" class="detail-page__icon-button">
                            <img src="{{ asset('storage/ハートロゴ_デフォルト.png') }}" alt="いいね">
                        </a>
                    @endguest

                    <span>{{ $likesCount }}</span>
                </div>

                <div class="detail-page__icon-group">
                    <img src="{{ asset('storage/ふきだしロゴ.png') }}" alt="コメント" class="detail-page__comment-icon">
                    <span>{{ $commentsCount }}</span>
                </div>
            </div>

            <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="detail-page__purchase-button">
                購入手続きへ
            </a>

            <section class="detail-page__section">
                <h2>商品説明</h2>
                <p class="detail-page__description">{{ $item->description }}</p>
            </section>

            <section class="detail-page__section">
                <h2>商品の情報</h2>

                <div class="detail-page__info-row">
                    <span class="detail-page__info-label">カテゴリー</span>
                    <div class="detail-page__category-list">
                        @foreach ($categories as $category)
                            <span class="detail-page__category">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="detail-page__info-row">
                    <span class="detail-page__info-label">商品の状態</span>
                    <span class="detail-page__condition">
                        @switch($item->condition)
                            @case(1) 良好 @break
                            @case(2) 目立った傷や汚れなし @break
                            @case(3) やや傷や汚れあり @break
                            @case(4) 状態が悪い @break
                        @endswitch
                    </span>
                </div>
            </section>

            <section class="detail-page__section">
                <h2>コメント({{ $commentsCount }})</h2>

                <div class="detail-page__comments">
                    @foreach ($comments as $comment)
                        <div class="detail-page__comment">
                            <div class="detail-page__comment-header">
                                <div class="detail-page__comment-image">
                                    @if(!empty($comment->profile_image))
                                        <img src="{{ asset($comment->profile_image) }}" alt="{{ $comment->profile_name }}">
                                    @endif
                                </div>
                                <span class="detail-page__comment-name">
                                    {{ $comment->profile_name ?? 'ユーザー' }}
                                </span>
                            </div>

                            <div class="detail-page__comment-body">
                                {{ $comment->body }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="detail-page__section">
                <h2>商品へのコメント</h2>

                @auth
                    <form action="{{ route('items.comment.store', ['item_id' => $item->id]) }}" method="post">
                        @csrf
                        <textarea name="comment" class="detail-page__textarea">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="error">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="detail-page__comment-submit">
                            コメントを送信する
                        </button>
                    </form>
                @endauth

                @guest
                    <textarea class="detail-page__textarea" onclick="window.location.href='/login'"readonly></textarea>

                    <a href="/login" class="detail-page__comment-submit detail-page__comment-submit--link">
                        コメントを送信する
                    </a>
                @endguest
            </section>
        </div>
    </div>
</div>
@endsection