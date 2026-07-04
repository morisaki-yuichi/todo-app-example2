<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * ログインフォームを表示する。
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * 認証を試みる。成功すればセッションを開始する。
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // attempt: email/passwordが一致するか照合(passwordはハッシュ比較)。
        // 一致すればログイン状態にする
        if (! auth()->attempt($credentials)) {
            // どの項目が違うか(email/password)は攻撃者に教えない。
            // 「認証情報が一致しない」という一括のエラーにする
            throw ValidationException::withMessages([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ]);
        }

        // セッション固定攻撃を防ぐため、ログイン時にセッションIDを再生成する
        $request->session()->regenerate();

        return redirect()->intended(route('todos.index'));
    }

    /**
     * ログアウトする。
     */
    public function destroy(Request $request)
    {
        auth()->logout();

        // セッションを無効化し、CSRFトークンも作り直す(ログアウトの後始末)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
