<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_todo_and_redirects_to_index(): void
    {
        $response = $this->post('/todos', [
            'title' => '新しいTODO',
            'description' => '説明文',
            'due_date' => '2026-08-01',
        ]);

        // PRG: 成功時は一覧へリダイレクト+フラッシュメッセージ
        $response->assertRedirect('/todos');
        $response->assertSessionHas('status', 'TODOを作成しました。');

        $this->assertDatabaseHas('todos', [
            'title' => '新しいTODO',
            'description' => '説明文',
        ]);
    }

    public function test_title_is_required(): void
    {
        $response = $this->post('/todos', ['title' => '']);

        // バリデーション違反は差し戻し+エラー。DBには入らない
        $response->assertSessionHasErrors('title');
        $this->assertDatabaseCount('todos', 0);
    }

    public function test_title_must_not_exceed_100_characters(): void
    {
        // 境界値: 100文字はOK、101文字はNG
        $this->post('/todos', ['title' => str_repeat('あ', 100)])
            ->assertSessionHasNoErrors();
        $this->post('/todos', ['title' => str_repeat('あ', 101)])
            ->assertSessionHasErrors('title');

        $this->assertDatabaseCount('todos', 1); // 100文字の1件だけ入る
    }

    public function test_invalid_due_date_is_rejected(): void
    {
        $this->post('/todos', ['title' => 'x', 'due_date' => 'not-a-date'])
            ->assertSessionHasErrors('due_date');
    }
}
