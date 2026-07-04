@extends('layouts.app')

@section('title', 'TODO一覧')

@section('content')
    <h2>TODO一覧</h2>

    <p><a href="{{ route('todos.create') }}">+ 新規作成</a></p>

    @if ($todos->isEmpty())
        <p>TODOがありません。</p>
    @else
        <ul>
            @foreach ($todos as $todo)
                <li>
                    <a href="{{ route('todos.show', $todo) }}">{{ $todo->title }}</a>
                    @if ($todo->completed)
                        (完了)
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@endsection
