@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-page">
    <form action="{{ route('purchase.store', ['item_id' => $item->id]) }}" method="post">
        @csrf

        <div class="purchase-page__inner">

            <div class="purchase-page__left">
                <div class="purchase-page__item">
                    <div class="purchase-page__image">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}">
                    </div>

                    <div class="purchase-page__item-info">
                        <h1 class="purchase-page__name">{{ $item->name }}</h1>
                        <p class="purchase-page__price">¥{{ number_format($item->price) }}</p>
                    </div>
                </div>

                <div class="purchase-page__section">
                    <label class="purchase-page__label">支払い方法</label>
                    <select name="payment_method" id="paymentMethodSelect" class="purchase-page__select">
                        <option value="">選択してください</option>
                        <option value="1" {{ old('payment_method') == 1 ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="2" {{ old('payment_method') == 2 ? 'selected' : '' }}>カード支払い</option>
                    </select>
                    @error('payment_method')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="purchase-page__section">
                    <div class="purchase-page__address-header">
                        <span class="purchase-page__label">配送先</span>
                        <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="purchase-page__change-link">
                            変更する
                        </a>
                    </div>

                    <div class="purchase-page__address">
                        <p>〒 {{ $shipping['post_code'] }}</p>
                        <p>{{ $shipping['address'] }}</p>
                        <p>{{ $shipping['building'] }}</p>
                    </div>
                </div>

                <div class="purchase-page__right purchase-page__right--sp">
                    <table class="purchase-page__summary">
                        <tr>
                            <th>商品代金</th>
                            <td>¥{{ number_format($item->price) }}</td>
                        </tr>
                        <tr>
                            <th>支払い方法</th>
                            <td id="paymentMethodTextSp">
                                @if(old('payment_method') == 1)
                                    コンビニ払い
                                @elseif(old('payment_method') == 2)
                                    カード支払い
                                @else
                                    ---
                                @endif
                            </td>
                        </tr>
                    </table>

                    <button type="submit" class="purchase-page__button">購入する</button>
                </div>
            </div>

            <div class="purchase-page__right">
                <table class="purchase-page__summary">
                    <tr>
                        <th>商品代金</th>
                        <td>¥{{ number_format($item->price) }}</td>
                    </tr>
                    <tr>
                        <th>支払い方法</th>
                        <td id="paymentMethodTextPc">
                            @if(old('payment_method') == 1)
                                コンビニ払い
                            @elseif(old('payment_method') == 2)
                                カード支払い
                            @else
                                ---
                            @endif
                        </td>
                    </tr>
                </table>

                <button type="submit" class="purchase-page__button">
                    購入する
                </button>
            </div>

        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const paymentSelect = document.getElementById('paymentMethodSelect');
    const paymentTextSp = document.getElementById('paymentMethodTextSp');
    const paymentTextPc = document.getElementById('paymentMethodTextPc');

    if (!paymentSelect) return;

    paymentSelect.addEventListener('change', function () {
        let text = '---';

        if (paymentSelect.value === '1') {
            text = 'コンビニ払い';
        } else if (paymentSelect.value === '2') {
            text = 'カード支払い';
        }

        if (paymentTextSp) paymentTextSp.textContent = text;
        if (paymentTextPc) paymentTextPc.textContent = text;
    });
});
</script>
@endsection