<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * 登録フォームを表示する。
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * 新規ユーザーを登録し、そのままログインさせる。
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // confirmed: password_confirmation フィールドとの一致を要求する
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Userモデルのpasswordは 'hashed' キャストなので、平文を渡すと自動でハッシュ化される
        $user = User::create($validated);

        // 登録後はそのままログイン状態にする(セッションを開始)
        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('todos.index')->with('status', 'ようこそ、' . $user->name . 'さん!');
    }
}
