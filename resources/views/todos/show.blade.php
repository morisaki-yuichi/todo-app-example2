@extends('layouts.app')

@section('title', $todo->title)

@section('content')
    <h2>{{ $todo->title }}</h2>

    <p>状態: {{ $todo->completed ? '完了' : '未完了' }}</p>

    {{-- 改行を保って表示する(white-space)。{{ }} で出すのでHTMLとしては解釈されない --}}
    <p style="white-space: pre-line">{{ $todo->description ?? '(内容はありません)' }}</p>

    <p>作成日時: {{ $todo->created_at->format('Y-m-d H:i') }}</p>

    <p><a href="{{ route('todos.edit', $todo) }}">このTODOを編集する</a></p>

    {{-- 確認ページへの遷移は「表示するだけ」なのでGETリンクでよい --}}
    <p><a href="{{ route('todos.confirmDestroy', $todo) }}">このTODOを削除する…</a></p>

    <p><a href="{{ route('todos.index') }}">← 一覧に戻る</a></p>
@endsection
