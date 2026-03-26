<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FREE-MARKET</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="{{ route('items.index') }}">
                <img class="brand__logo" src="{{ asset('storage/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            </a>

            <form action="{{ route('items.index') }}" method="get" class="header__search">
                <input
                    class="header-search__input"
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="なにをお探しですか？"
                >
                <input type="hidden" name="tab" value="{{ request('tab', 'recommend') }}">
            </form>

            <nav class="header-nav">
                @auth
                    <form action="/logout" method="post" class="header-nav__form">
                        @csrf
                        <button type="submit" class="header-nav__button">ログアウト</button>
                    </form>

                    <a class="header-nav__link" href="{{ route('profile.show') }}">マイページ</a>
                    <a class="header-nav__sell" href="/sell">出品</a>
                @endauth

                @guest
                    <a class="header-nav__link" href="/login">ログイン</a>
                    <a class="header-nav__link" href="/login">マイページ</a>
                    <a class="header-nav__sell" href="/login">出品</a>
                @endguest
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>