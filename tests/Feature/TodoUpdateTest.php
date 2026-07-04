<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoUpdateTest extends TestCase
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

    public function test_can_update_todo(): void
    {
        $todo = Todo::factory()->for($this->user)->create(['title' => '古いタイトル']);

        $response = $this->put("/todos/{$todo->id}", [
            'title' => '新しいタイトル',
            'description' => '更新後の説明',
        ]);

        $response->assertRedirect("/todos/{$todo->id}");
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => '新しいタイトル',
        ]);
    }

    public function test_update_validates_like_create(): void
    {
        $todo = Todo::factory()->for($this->user)->create(['title' => '元のまま']);

        $this->put("/todos/{$todo->id}", ['title' => ''])
            ->assertSessionHasErrors('title');

        // 差し戻されたので値は変わっていない
        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'title' => '元のまま']);
    }

    public function test_can_toggle_completed(): void
    {
        $todo = Todo::factory()->for($this->user)->create(['completed' => false]);

        $this->patch("/todos/{$todo->id}/toggle")->assertRedirect('/todos');
        $this->assertTrue($todo->fresh()->completed);

        // もう一度で戻る(双方向)
        $this->patch("/todos/{$todo->id}/toggle");
        $this->assertFalse($todo->fresh()->completed);
    }
}
