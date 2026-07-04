@extends('layouts.app')

@section('title', 'TODOの編集')

@section('content')
    <h2>TODOの編集</h2>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('todos.update', $todo) }}">
        @csrf
        {{-- HTMLのformはGET/POSTしか送れないため、「本当はPUT」を隠しフィールドで
             伝える(メソッドスプーフィング)。Laravelがこれを見てPUTルートに振り分ける --}}
        @method('PUT')
        <p>
            <label for="title">タイトル(必須・100文字まで)</label><br>
            {{-- old()の第2引数: 差し戻し時はold値、初回表示は現在のDB値を出す --}}
            <input type="text" id="title" name="title" size="40" value="{{ old('title', $todo->title) }}">
        </p>
        <p>
            <label for="description">内容(任意・1000文字まで)</label><br>
            <textarea id="description" name="description" rows="5" cols="40">{{ old('description', $todo->description) }}</textarea>
        </p>
        <p>
            <button type="submit">更新する</button>
        </p>
    </form>

    <p><a href="{{ route('todos.show', $todo) }}">← 詳細に戻る</a></p>
@endsection
