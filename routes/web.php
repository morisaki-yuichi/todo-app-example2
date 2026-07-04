<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

// トップはTODO一覧へ(このアプリの「顔」は一覧のため)。301ではなく302なのは
// 恒久的な転送のキャッシュ(ブラウザが/を覚え込む)を避ける開発中の定石
Route::redirect('/', '/todos');

// 認証系: guestミドルウェア=ログイン済みなら弾く(ログイン中に登録画面は不要)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// ログアウトはログイン済みのみ
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// TODO系: authミドルウェアで保護。未ログインは自動で /login へリダイレクトされる
Route::middleware('auth')->group(function () {
    Route::get('/todos', [TodoController::class, 'index'])->name('todos.index');
    // 「/todos/create」は「/todos/{todo}」より先に定義する。
    // ルートは定義順にマッチするため、逆だと "create" がTODOのIDとして解釈され404になる
    Route::get('/todos/create', [TodoController::class, 'create'])->name('todos.create');
    Route::post('/todos', [TodoController::class, 'store'])->name('todos.store');
    Route::get('/todos/{todo}', [TodoController::class, 'show'])->name('todos.show');
    Route::get('/todos/{todo}/edit', [TodoController::class, 'edit'])->name('todos.edit');
    Route::put('/todos/{todo}', [TodoController::class, 'update'])->name('todos.update');
    Route::patch('/todos/{todo}/toggle', [TodoController::class, 'toggle'])->name('todos.toggle');
    // 確認ページ(GET=表示するだけ)と削除の実行(DELETE)を分ける。GETでデータを変えない
    Route::get('/todos/{todo}/delete', [TodoController::class, 'confirmDestroy'])->name('todos.confirmDestroy');
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])->name('todos.destroy');
});
