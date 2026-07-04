@extends('layouts.app')

@section('title', 'TODO一覧')

@section('content')
    <h2>TODO一覧</h2>

    <p><a href="{{ route('todos.create') }}">+ 新規作成</a></p>

    {{-- 絞り込みは「見るだけ」なのでGETフォーム(データを変えないならGETが正解)。
         GETフォームにはCSRFトークン不要(何も変更しないため) --}}
    <form method="GET" action="{{ route('todos.index') }}" class="filter">
        <select name="status">
            <option value="all" @selected($status === 'all')>すべて</option>
            <option value="open" @selected($status === 'open')>未完了のみ</option>
            <option value="done" @selected($status === 'done')>完了のみ</option>
        </select>
        <input type="text" name="keyword" value="{{ $keyword }}" placeholder="キーワード(タイトル・内容)">
        <button type="submit">絞り込む</button>
        @if ($status !== 'all' || $keyword !== '')
            <a href="{{ route('todos.index') }}">解除</a>
        @endif
    </form>

    @if ($todos->isEmpty())
        @if ($status !== 'all' || $keyword !== '')
            <p>条件に一致するTODOがありません。</p>
        @else
            <p>TODOがありません。</p>
        @endif
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
