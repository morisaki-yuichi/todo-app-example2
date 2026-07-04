<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_displays_todo_details(): void
    {
        $todo = Todo::factory()->create([
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
