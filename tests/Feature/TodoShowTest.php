<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoShowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // 各テストの前にユーザーを作ってログイン。以降のTODOはこの人の所有にする
        // (スプリント8で認可を入れたため、他人のTODOは403になる)
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_show_displays_todo_details(): void
    {
        $todo = Todo::factory()->for($this->user)->create([
            'title' => '歯医者を予約する',
            'description' => '午前中がよい',
        ]);

        $this->get("/todos/{$todo->id}")
            ->assertOk()
            ->assertSee('歯医者を予約する')
            ->assertSee('午前中がよい');
    }

    public function test_show_returns_404_for_missing_todo(): void
    {
        // ルートモデルバインディングが存在しないIDで404を返すことを固定する
        $this->get('/todos/999999')->assertNotFound();
    }
}
