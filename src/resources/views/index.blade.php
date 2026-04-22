@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css')}}">
@endsection

@section('content')
<div class="items-page">

  {{-- タブ --}}
  <div class="tabs">
    <a class="tab {{ $tab === 'recommend' ? 'is-active' : '' }}"
       href="{{ route('items.index', array_filter(['tab' => 'recommend', 'q' => $q])) }}">
      おすすめ
    </a>

    <a class="tab {{ $tab === 'mylist' ? 'is-active' : '' }}"
       href="{{ route('items.index', array_filter(['tab' => 'mylist', 'q' => $q])) }}">
      マイリスト
    </a>
  </div>

  {{-- 一覧 --}}
  <div class="grid">
    @forelse($items as $item)
      <a href="{{ route('items.detail', $item->id) }}" class="card-link">
        <div class="thumb">
          <img src="{{ asset($item->image) }}">
          @if((int)$item->status === 3)
            <span class="sold">Sold</span>
          @endif
        </div>
        <div class="name">{{ $item->name }}</div>
        </a>
    @empty
      <p class="empty">
        @if($tab === 'mylist' && !auth()->check())

        @else
          商品がありません。
        @endif
      </p>
    @endforelse
  </div>

</div>
@endsection