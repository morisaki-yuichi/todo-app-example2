<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

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
     * TODOの新規作成フォームを表示する。
     */
    public function create()
    {
        return view('todos.create');
    }

    /**
     * フォームから送られたTODOを保存する。
     */
    public function store(Request $request)
    {
        // 違反時はここで処理が止まり、エラーと入力値(old)を持って
        // 自動でフォームへ302リダイレクトされる(自動差し戻し)
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $todo = Todo::create($validated);

        // PRGパターン: POSTの結果は「リダイレクト」で返す。
        // 直接HTMLを返すと、ブラウザのリロードでPOSTが再送されて二重登録される。
        // with()はフラッシュデータ: 次の1リクエストだけ生きるセッション値
        return redirect()->route('todos.index')->with('status', 'TODOを作成しました。');
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
