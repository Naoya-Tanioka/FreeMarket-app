<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FREE-MARKET</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    @yield('css')
</head>

    <body>
        <header class="header">
            <div class="header__inner">
                <a class="header__logo" href="{{ route('items.index') }}">
                    <img class ="brand__logo" src="{{ asset('storage/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
                </a>
                <div class="header__actions">
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

    </body>
</html>