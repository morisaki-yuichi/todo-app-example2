<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * TODO一覧を表示する(状態・キーワードの絞り込みつき)。
     *
     * 絞り込みは「見るだけ」の操作なのでGET+クエリパラメータで受ける。
     * URLに条件が現れるため、結果を共有・ブックマークできる。
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');   // all / open / done
        $keyword = $request->query('keyword', '');

        $query = Todo::query();

        if ($status === 'open') {
            $query->where('completed', false);
        } elseif ($status === 'done') {
            $query->where('completed', true);
        } // 想定外の値は「すべて」として扱う(不正な値で500にしない)

        if ($keyword !== '') {
            // orWhereはクロージャで括って (title LIKE .. OR description LIKE ..) に
            // まとめる。括らないと status の条件がORで無効化されるバグになる
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // 新しい順。同一秒作成に備えてidを第2ソートキーにする(スプリント2の実録)。
        // paginate(5)で5件ずつに分割。withQueryString()を付けると、ページ移動リンクに
        // 現在の絞り込み条件(status/keyword)が引き継がれる(付けないと2ページ目で条件が消える)
        $todos = $query->orderByDesc('created_at')->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        return view('todos.index', [
            'todos' => $todos,
            'status' => $status,
            'keyword' => $keyword,
        ]);
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
            'due_date' => ['nullable', 'date'], // 任意・過去日も許可(qa-log参照)
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
            'due_date' => ['nullable', 'date'],
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
     * 削除の確認ページを表示する(表示するだけ。まだ消さない)。
     *
     * JSのconfirm()を使わない本プロジェクトでは、誤操作防止の
     * 確認ステップを「確認ページ」として実装する。
     */
    public function confirmDestroy(Todo $todo)
    {
        return view('todos.confirm-destroy', ['todo' => $todo]);
    }

    /**
     * TODOを削除する(確認ページのフォームからのDELETEでのみ実行される)。
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();

        return redirect()->route('todos.index')->with('status', 'TODOを削除しました。');
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
