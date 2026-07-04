@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
    <h2>ログイン</h2>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <p>
            <label for="email">メールアドレス</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
        </p>
        <p>
            <label for="password">パスワード</label><br>
            <input type="password" id="password" name="password">
        </p>
        <p>
            <button type="submit">ログイン</button>
        </p>
    </form>

    <p>アカウントをお持ちでないですか? <a href="{{ route('register') }}">新規登録</a></p>
@endsection
