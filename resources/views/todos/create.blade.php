@extends('layouts.app')

@section('title', 'TODOの新規作成')

@section('content')
    <h2>TODOの新規作成</h2>

    {{-- バリデーションで差し戻されたとき、エラー内容をまとめて表示する --}}
    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('todos.store') }}">
        @csrf {{-- CSRF対策のトークン。POSTフォームには必須(なしだと419になる) --}}
        <p>
            <label for="title">タイトル(必須・100文字まで)</label><br>
            {{-- old(): 差し戻し時に直前の入力値を復元する(消えると入力し直しでUX最悪) --}}
            <input type="text" id="title" name="title" size="40" value="{{ old('title') }}">
        </p>
        <p>
            <label for="description">内容(任意・1000文字まで)</label><br>
            <textarea id="description" name="description" rows="5" cols="40">{{ old('description') }}</textarea>
        </p>
        <p>
            <button type="submit">作成する</button>
        </p>
    </form>

    <p><a href="{{ route('todos.index') }}">← 一覧に戻る</a></p>
@endsection
