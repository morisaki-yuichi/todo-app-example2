<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoDeleteTest extends TestCase
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

    public function test_confirm_page_does_not_delete(): void
    {
        $todo = Todo::factory()->for($this->user)->create();

        // 確認ページはGET=表示のみ。レコードは残る
        $this->get("/todos/{$todo->id}/delete")->assertOk();
        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
    }

    public function test_delete_removes_only_the_target(): void
    {
        $target = Todo::factory()->for($this->user)->create();
        $other = Todo::factory()->for($this->user)->create();

        $this->delete("/todos/{$target->id}")->assertRedirect('/todos');

        // 対象は消え、他は残る(全削除バグの検出。スプリント4レトロのT-9)
        $this->assertDatabaseMissing('todos', ['id' => $target->id]);
        $this->assertDatabaseHas('todos', ['id' => $other->id]);
    }
}
