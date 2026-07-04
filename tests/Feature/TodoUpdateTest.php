<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // スプリント7で認証必須にしたため、各テストの前にログイン状態を作る。
        // このアプリでは誰がログインしていてもTODOは共通(所有権はスプリント8)
        $this->actingAs(User::factory()->create());
    }

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
