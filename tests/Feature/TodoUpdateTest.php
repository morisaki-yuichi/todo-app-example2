<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_todo(): void
    {
        $todo = Todo::factory()->create(['title' => '古いタイトル']);

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
        $todo = Todo::factory()->create(['title' => '元のまま']);

        $this->put("/todos/{$todo->id}", ['title' => ''])
            ->assertSessionHasErrors('title');

        // 差し戻されたので値は変わっていない
        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'title' => '元のまま']);
    }

    public function test_can_toggle_completed(): void
    {
        $todo = Todo::factory()->create(['completed' => false]);

        $this->patch("/todos/{$todo->id}/toggle")->assertRedirect('/todos');
        $this->assertTrue($todo->fresh()->completed);

        // もう一度で戻る(双方向)
        $this->patch("/todos/{$todo->id}/toggle");
        $this->assertFalse($todo->fresh()->completed);
    }
}
