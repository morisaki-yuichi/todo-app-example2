@extends('layouts.app')

@section('title', 'TODO一覧')

@section('content')
    <h2>TODO一覧</h2>

    @if ($todos->isEmpty())
        <p>TODOがありません。</p>
    @else
        <ul>
            @foreach ($todos as $todo)
                <li>
                    {{ $todo->title }}
                    @if ($todo->completed)
                        (完了)
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@endsection
