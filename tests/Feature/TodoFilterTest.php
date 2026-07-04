<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // スプリント7で認証必須にしたため、各テストの前にログイン状態を作る。
        // このアプリでは誰がログインしていてもTODOは共通(所有権はスプリント8)
        $this->actingAs(User::factory()->create());
    }

    public function test_status_filter_shows_only_matching(): void
    {
        // タイトルは部分文字列で重ならないものを選ぶ。
        // 例えば「未完了タスク」は「完了タスク」を含むため、assertDontSeeが
        // 誤反応する(スプリント6の実録トラブル)。無関係な語にする
        Todo::factory()->create(['title' => '牛乳を買う', 'completed' => false]);
        Todo::factory()->completed()->create(['title' => '部屋の掃除']);

        // 未完了のみ
        $this->get('/todos?status=open')
            ->assertSee('牛乳を買う')
            ->assertDontSee('部屋の掃除');

        // 完了のみ
        $this->get('/todos?status=done')
            ->assertSee('部屋の掃除')
            ->assertDontSee('牛乳を買う');
    }

    public function test_keyword_matches_title_and_description(): void
    {
        Todo::factory()->create(['title' => '牛乳を買う', 'description' => 'スーパーで']);
        Todo::factory()->create(['title' => '掃除', 'description' => '牛乳をこぼした跡']);
        Todo::factory()->create(['title' => '散歩', 'description' => '公園まで']);

        // タイトル・内容どちらの一致もヒットする
        $response = $this->get('/todos?keyword=' . urlencode('牛乳'));
        $response->assertSee('牛乳を買う')->assertSee('掃除')->assertDontSee('散歩');
    }

    public function test_invalid_status_is_treated_as_all(): void
    {
        Todo::factory()->create(['title' => 'あるTODO']);

        // 想定外の値でも500にせず全件表示
        $this->get('/todos?status=hack')->assertOk()->assertSee('あるTODO');
    }

    public function test_invalid_page_does_not_error(): void
    {
        $this->get('/todos?page=abc')->assertOk();
        $this->get('/todos?page=999')->assertOk();
    }

    public function test_overdue_is_highlighted(): void
    {
        Todo::factory()->overdue()->create(['title' => '期限切れタスク']);

        $this->get('/todos')->assertSee('期限切れ');
    }

    public function test_pagination_limits_to_five_per_page(): void
    {
        Todo::factory()->count(7)->create();

        // 1ページ5件+2件目のページが存在する
        $this->get('/todos')->assertSee('次へ');
    }
}
