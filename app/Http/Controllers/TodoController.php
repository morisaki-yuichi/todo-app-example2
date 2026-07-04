<?php

namespace App\Http\Controllers;

use App\Models\Todo;

class TodoController extends Controller
{
    /**
     * TODO一覧を表示する。
     */
    public function index()
    {
        // 新しい順。シーダーのように同一秒で複数作られると
        // created_atだけでは並びが不安定なため、idを第2ソートキーにする
        $todos = Todo::orderByDesc('created_at')->orderByDesc('id')->get();

        return view('todos.index', ['todos' => $todos]);
    }

    /**
     * TODOの詳細を表示する。
     *
     * 引数の型をTodoにするとLaravelがURLの{todo}からレコードを
     * 自動で取得してくれる(ルートモデルバインディング)。
     * 存在しないIDなら自動で404になる。
     */
    public function show(Todo $todo)
    {
        return view('todos.show', ['todo' => $todo]);
    }
}
