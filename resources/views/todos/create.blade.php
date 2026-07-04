@extends('layouts.app')

@section('title', 'TODOの新規作成')

@section('content')
    <h2>TODOの新規作成</h2>

    <form method="POST" action="{{ route('todos.store') }}">
        @csrf {{-- CSRF対策のトークン。POSTフォームには必須(なしだと419になる) --}}
        <p>
            <label for="title">タイトル(必須・100文字まで)</label><br>
            <input type="text" id="title" name="title" size="40">
        </p>
        <p>
            <label for="description">内容(任意・1000文字まで)</label><br>
            <textarea id="description" name="description" rows="5" cols="40"></textarea>
        </p>
        <p>
            <button type="submit">作成する</button>
        </p>
    </form>

    <p><a href="{{ route('todos.index') }}">← 一覧に戻る</a></p>
@endsection
