<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * TODO作成のバリデーションを担うFormRequest。
 * コントローラの store() から検証ロジックを切り出して集約した。
 */
class StoreTodoRequest extends FormRequest
{
    /**
     * このリクエストを実行してよいか。
     * 作成は「ログイン済みなら自分のTODOを作れる」ので、ルートのauthミドルウェアに
     * 任せてここではtrue(falseのままだと常に403になるので注意)。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール。
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
