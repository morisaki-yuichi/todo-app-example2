<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoIndexTest extends TestCase
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

    public function test_index_shows_todo_titles(): void
    {
        Todo::factory()->for($this->user)->create(['title' => '牛乳を買う']);

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
