@extends('layouts.app')

@section('title', 'アカウント登録')

@section('content')
    <h2>アカウント登録</h2>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <p>
            <label for="name">名前</label><br>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
        </p>
        <p>
            <label for="email">メールアドレス</label><br>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
        </p>
        <p>
            <label for="password">パスワード(8文字以上)</label><br>
            {{-- パスワードはold()で復元しない(値を画面に残すのは危険) --}}
            <input type="password" id="password" name="password">
        </p>
        <p>
            <label for="password_confirmation">パスワード(確認)</label><br>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </p>
        <p>
            <button type="submit">登録する</button>
        </p>
    </form>

    <p>すでにアカウントをお持ちですか? <a href="{{ route('login') }}">ログイン</a></p>
@endsection
