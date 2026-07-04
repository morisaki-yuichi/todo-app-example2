<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // スプリント7で認証必須にしたため、各テストの前にログイン状態を作る。
        // このアプリでは誰がログインしていてもTODOは共通(所有権はスプリント8)
        $this->actingAs(User::factory()->create());
    }

    public function test_confirm_page_does_not_delete(): void
    {
        $todo = Todo::factory()->create();

        // 確認ページはGET=表示のみ。レコードは残る
        $this->get("/todos/{$todo->id}/delete")->assertOk();
        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
    }

    public function test_delete_removes_only_the_target(): void
    {
        $target = Todo::factory()->create();
        $other = Todo::factory()->create();

        $this->delete("/todos/{$target->id}")->assertRedirect('/todos');

        // 対象は消え、他は残る(全削除バグの検出。スプリント4レトロのT-9)
        $this->assertDatabaseMissing('todos', ['id' => $target->id]);
        $this->assertDatabaseHas('todos', ['id' => $other->id]);
    }
}
