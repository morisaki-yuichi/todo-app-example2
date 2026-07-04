<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 認可(誰が何を触れるか)のテスト。
 * 各操作を「本人はできる / 他人は403」のペアで検証する(スプリント7レトロのT-17)。
 */
class TodoAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $stranger;
    private Todo $todo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->stranger = User::factory()->create();
        $this->todo = Todo::factory()->for($this->owner)->create();
    }

    public function test_index_shows_only_own_todos(): void
    {
        Todo::factory()->for($this->owner)->create(['title' => '自分のTODO']);
        Todo::factory()->for($this->stranger)->create(['title' => '他人のTODO']);

        $this->actingAs($this->owner)
            ->get('/todos')
            ->assertSee('自分のTODO')
            ->assertDontSee('他人のTODO');
    }

    public function test_owner_can_view_but_stranger_gets_403(): void
    {
        $this->actingAs($this->owner)->get("/todos/{$this->todo->id}")->assertOk();
        $this->actingAs($this->stranger)->get("/todos/{$this->todo->id}")->assertForbidden();
    }

    public function test_owner_can_edit_but_stranger_gets_403(): void
    {
        $this->actingAs($this->owner)->get("/todos/{$this->todo->id}/edit")->assertOk();
        $this->actingAs($this->stranger)->get("/todos/{$this->todo->id}/edit")->assertForbidden();
    }

    public function test_owner_can_update_but_stranger_gets_403(): void
    {
        $this->actingAs($this->owner)
            ->put("/todos/{$this->todo->id}", ['title' => '本人が更新'])
            ->assertRedirect();

        $this->actingAs($this->stranger)
            ->put("/todos/{$this->todo->id}", ['title' => '他人が更新'])
            ->assertForbidden();

        // 他人の更新はDBに反映されていない
        $this->assertDatabaseHas('todos', ['id' => $this->todo->id, 'title' => '本人が更新']);
    }

    public function test_owner_can_toggle_but_stranger_gets_403(): void
    {
        $this->actingAs($this->stranger)
            ->patch("/todos/{$this->todo->id}/toggle")
            ->assertForbidden();

        $this->actingAs($this->owner)
            ->patch("/todos/{$this->todo->id}/toggle")
            ->assertRedirect();
    }

    public function test_owner_can_delete_but_stranger_gets_403(): void
    {
        // 他人は削除できない(レコードは残る)
        $this->actingAs($this->stranger)
            ->delete("/todos/{$this->todo->id}")
            ->assertForbidden();
        $this->assertDatabaseHas('todos', ['id' => $this->todo->id]);

        // 本人は削除できる
        $this->actingAs($this->owner)
            ->delete("/todos/{$this->todo->id}")
            ->assertRedirect('/todos');
        $this->assertDatabaseMissing('todos', ['id' => $this->todo->id]);
    }

    public function test_new_todo_belongs_to_creator(): void
    {
        $this->actingAs($this->owner)->post('/todos', ['title' => '作成者のTODO']);

        $this->assertDatabaseHas('todos', [
            'title' => '作成者のTODO',
            'user_id' => $this->owner->id,
        ]);
    }
}
