<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * 動作確認用のダミーTODOを投入する。
     * スプリント8以降はTODOに所有者(user_id)が必須。
     * デモユーザーと、別ユーザー(認可の403確認用)の2人分を作る。
     */
    public function run(): void
    {
        // 再実行しても増殖しないよう、入れ直す
        Todo::query()->delete();

        $demo = User::where('email', 'demo@example.com')->first();
        // 認可(他人のTODOは403)を試すための2人目
        $other = User::updateOrCreate(
            ['email' => 'other@example.com'],
            ['name' => '別のユーザー', 'password' => 'password'],
        );

        // --- デモユーザーのTODO(表示分岐の目視用) ---
        $this->makeForUser($demo->id, [
            ['title' => '牛乳を買う', 'description' => "低脂肪ではなく普通のもの。\nついでに卵も。", 'due' => now()->subDay(), 'done' => false, 'ago' => 2],
            ['title' => 'Laravel教材のスプリント1を写経する', 'description' => '環境構築とREADMEの手順を追体験する', 'due' => now()->subDay(), 'done' => true, 'ago' => 1],
            ['title' => '部屋の掃除', 'description' => null, 'due' => null, 'done' => false, 'ago' => 0],
        ]);

        // ページネーション確認用の追加(デモユーザー・合計12件)
        $samples = [
            '請求書を確認する', '歯医者を予約する', '本を返却する', '観葉植物に水やり',
            'バックアップを取る', '会議の議事録を書く', 'ランニング30分', '銀行で振り込み',
            'レシピを調べる',
        ];
        foreach ($samples as $i => $title) {
            $this->makeForUser($demo->id, [
                ['title' => $title, 'description' => null, 'due' => null, 'done' => $i % 3 === 0, 'ago' => 3 + $i],
            ]);
        }

        // --- 別ユーザーのTODO(認可確認用) ---
        $this->makeForUser($other->id, [
            ['title' => '他人のTODO(見えてはいけない)', 'description' => '別ユーザー所有', 'due' => null, 'done' => false, 'ago' => 1],
        ]);
    }

    /**
     * created_atをずらしつつ、指定ユーザーのTODOを作る補助メソッド。
     *
     * @param  array<int, array{title:string, description:?string, due:mixed, done:bool, ago:int}>  $rows
     */
    private function makeForUser(int $userId, array $rows): void
    {
        foreach ($rows as $r) {
            $todo = new Todo([
                'user_id' => $userId,
                'title' => $r['title'],
                'description' => $r['description'],
                'due_date' => $r['due']?->toDateString(),
                'completed' => $r['done'],
            ]);
            $todo->created_at = now()->subDays($r['ago']);
            $todo->save();
        }
    }
}
