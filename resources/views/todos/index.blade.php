@extends('layouts.app')

@section('title', 'TODO一覧')

@section('content')
    <h2>TODO一覧</h2>

    <p><a href="{{ route('todos.create') }}">+ 新規作成</a></p>

    @if ($todos->isEmpty())
        <p>TODOがありません。</p>
    @else
        <ul class="todo-list">
            @foreach ($todos as $todo)
                <li class="{{ $todo->completed ? 'completed' : '' }}">
                    <a href="{{ route('todos.show', $todo) }}">{{ $todo->title }}</a>
                    @if ($todo->completed)
                        (完了)
                    @endif
                    @if ($todo->due_date)
                        <span class="due {{ $todo->isOverdue() ? 'overdue' : '' }}">
                            期限: {{ $todo->due_date->format('m/d') }}@if ($todo->isOverdue())(期限切れ)@endif
                        </span>
                    @endif
                    {{-- 状態変更はGETリンクではなくフォーム(PATCH)で行う --}}
                    <form method="POST" action="{{ route('todos.toggle', $todo) }}" style="display: inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit">{{ $todo->completed ? '未完了に戻す' : '完了にする' }}</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
