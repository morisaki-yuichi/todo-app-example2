<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * TODO更新のバリデーションを担うFormRequest。
 *
 * 現状はStoreと同一ルールだが、あえて別クラスにしている:
 * 「作成と更新は将来別ルールになりうる」ため(例: 更新だけ完了状態を扱う等)。
 * 共通化(1クラス)と拡張性(2クラス)の天秤で、拡張性を選んだDRYの実例。
 * なお認可(持ち主か)はコントローラの $this->authorize('update', $todo) が担う。
 */
class UpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
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
