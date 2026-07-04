<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

/**
 * TODOに対して「誰が何をしてよいか」を判断する認可ルール。
 * どのメソッドも「そのTODOの持ち主か?」だけを見る。
 * コントローラから $this->authorize('view', $todo) のように呼ぶと、
 * falseのとき自動で403(Forbidden)になる。
 */
class TodoPolicy
{
    /**
     * 詳細の閲覧: 持ち主だけ。
     */
    public function view(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    /**
     * 更新(編集・完了トグル): 持ち主だけ。
     */
    public function update(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }

    /**
     * 削除: 持ち主だけ。
     */
    public function delete(User $user, Todo $todo): bool
    {
        return $user->id === $todo->user_id;
    }
}
