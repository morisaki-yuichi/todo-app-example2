<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoFactorySmokeTest extends TestCase
{
    // RefreshDatabase: 各テストの前にtesting DBを作り直す(テスト間の汚染を防ぐ)
    use RefreshDatabase;

    public function test_factory_creates_a_todo(): void
    {
        $todo = Todo::factory()->create();

        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
        // 修正: 既定は未完了(false)。赤を見てから緑にするのがTDDのリズム
        $this->assertFalse($todo->completed);
    }

    public function test_completed_state_works(): void
    {
        $todo = Todo::factory()->completed()->create();

        $this->assertTrue($todo->completed);
    }
}
