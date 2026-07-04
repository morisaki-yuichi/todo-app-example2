<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- 各ページのタイトルは @yield で差し込む。アプリ名は設定(config)から取る --}}
    <title>@yield('title') | {{ config('app.name') }}</title>
</head>
<body>
    <header>
        <h1>{{ config('app.name') }}</h1>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
