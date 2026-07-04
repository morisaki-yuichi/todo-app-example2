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
     * TODOの編集フォームを表示する。
     */
    public function edit(Todo $todo)
    {
        return view('todos.edit', ['todo' => $todo]);
    }

    /**
     * 編集フォームの内容でTODOを更新する。
     */
    public function update(Request $request, Todo $todo)
    {
        // ルールは作成時(store)と同一。挙動を揃えることが仕様(US-4)
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $todo->update($validated);

        return redirect()->route('todos.show', $todo)->with('status', 'TODOを更新しました。');
    }

    /**
     * TODOの完了/未完了を切り替える。
     *
     * PUTではなくPATCHなのは「リソースの一部だけを変更する」操作のため。
     * 入力値は使わない(現在値の反転)のでバリデーションは不要。
     */
    public function toggle(Todo $todo)
    {
        $todo->update(['completed' => ! $todo->completed]);

        $message = $todo->completed ? 'TODOを完了にしました。' : 'TODOを未完了に戻しました。';

        return redirect()->route('todos.index')->with('status', $message);
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
