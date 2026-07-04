<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_todo_titles(): void
    {
        Todo::factory()->create(['title' => '牛乳を買う']);

        $this->get('/todos')
            ->assertOk()
            ->assertSee('牛乳を買う');
    }

    public function test_index_shows_empty_message_when_no_todos(): void
    {
        $this->get('/todos')
            ->assertOk()
            ->assertSee('TODOがありません');
    }

    public function test_root_redirects_to_todos(): void
    {
        // スプリント4で / を /todos へリダイレクトにした振る舞いを固定する
        $this->get('/')->assertRedirect('/todos');
    }
}
