<?php

namespace Tests\Unit;

use App\Models\Todo;
use Tests\TestCase;

/**
 * isOverdue()の判定ロジックだけを検証するテスト。
 * DBには保存しない(new)が、モデルのdateキャストがLaravelアプリの
 * 起動を必要とするため、素のPHPUnitではなくTests\TestCaseを継承する。
 */
class TodoOverdueTest extends TestCase
{
    public function test_past_due_and_incomplete_is_overdue(): void
    {
        $todo = new Todo(['due_date' => now()->subDay(), 'completed' => false]);
        $this->assertTrue($todo->isOverdue());
    }

    public function test_completed_is_never_overdue(): void
    {
        $todo = new Todo(['due_date' => now()->subDay(), 'completed' => true]);
        $this->assertFalse($todo->isOverdue());
    }

    public function test_no_due_date_is_not_overdue(): void
    {
        $todo = new Todo(['due_date' => null, 'completed' => false]);
        $this->assertFalse($todo->isOverdue());
    }

    public function test_today_is_not_overdue(): void
    {
        // 「今日が期限」はまだ間に合うので期限切れではない(境界)
        $todo = new Todo(['due_date' => today(), 'completed' => false]);
        $this->assertFalse($todo->isOverdue());
    }
}
