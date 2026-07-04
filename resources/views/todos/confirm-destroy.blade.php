@extends('layouts.app')

@section('title', '削除の確認')

@section('content')
    <h2>削除の確認</h2>

    <p>次のTODOを削除します。<strong>この操作は取り消せません。</strong></p>

    <p>「{{ $todo->title }}」</p>

    <form method="POST" action="{{ route('todos.destroy', $todo) }}">
        @csrf
        @method('DELETE')
        <button type="submit">削除する</button>
    </form>

    <p><a href="{{ route('todos.show', $todo) }}">キャンセル(詳細に戻る)</a></p>
@endsection
