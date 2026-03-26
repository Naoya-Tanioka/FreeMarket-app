@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase_address.css') }}">
@endsection

@section('content')
<div class="purchase-address-page">
        <h1 class="purchase-address-page__title">住所の変更</h1>

        <form action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}" method="post" class="purchase-address-form">
            @csrf
            @method('PUT')
            <div class="form__group">
                <label class="label">郵便番号</label>
                <input
                    class="input"
                    type="text"
                    name="post_code"
                    value="{{ old('post_code', $shipping['post_code']) }}"
                >
                @error('post_code')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form__group">
                <label class="label">住所</label>
                <input
                    class="input"
                    type="text"
                    name="address"
                    value="{{ old('address', $shipping['address']) }}"
                >
                @error('address')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form__group">
                <label class="label">建物名</label>
                <input
                    class="input"
                    type="text"
                    name="building"
                    value="{{ old('building', $shipping['building']) }}"
                >
                @error('building')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="actions">
                <button class="update__btn" type="submit">更新する</button>
            </div>
        </form>
</div>
@endsection