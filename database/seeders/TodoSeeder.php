<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * 動作確認用のダミーTODOを投入する。
     * 完了/未完了、内容あり/なしを意図的に混在させ、
     * 画面側の表示分岐をすべて目視できるようにしている。
     */
    public function run(): void
    {
        // 再実行しても増殖しないよう、入れ直す
        Todo::query()->delete();

        // created_atを意図的にずらす:全件が同一秒だと「新しい順」の並びを
        // 目視で検証できない(同値ソートは順序不定)ため。スプリント2レトロのTry
        // due_dateは3パターン用意する:
        // 期限切れ(過去日+未完了)/過去日だが完了済み(強調されない)/期限なし
        $todo = new Todo([
            'title' => '牛乳を買う',
            'description' => "低脂肪ではなく普通のもの。\nついでに卵も。",
            'due_date' => now()->subDay()->toDateString(), // 期限切れケース
            'completed' => false,
        ]);
        $todo->created_at = now()->subDays(2);
        $todo->save();

        $todo = new Todo([
            'title' => 'Laravel教材のスプリント1を写経する',
            'description' => '環境構築とREADMEの手順を追体験する',
            'due_date' => now()->subDay()->toDateString(), // 過去日でも完了済みなら強調しない
            'completed' => true,
        ]);
        $todo->created_at = now()->subDay();
        $todo->save();

        $todo = new Todo([
            'title' => '部屋の掃除',
            'description' => null, // 内容なしのTODO(詳細画面の表示確認用)
            'due_date' => null,    // 期限なしケース(既存データ相当)
            'completed' => false,
        ]);
        $todo->created_at = now();
        $todo->save();
    }
}
