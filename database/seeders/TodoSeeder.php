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

        Todo::create([
            'title' => '牛乳を買う',
            'description' => "低脂肪ではなく普通のもの。\nついでに卵も。",
            'completed' => false,
        ]);

        Todo::create([
            'title' => 'Laravel教材のスプリント1を写経する',
            'description' => '環境構築とREADMEの手順を追体験する',
            'completed' => true,
        ]);

        Todo::create([
            'title' => '部屋の掃除',
            'description' => null, // 内容なしのTODO(詳細画面の表示確認用)
            'completed' => false,
        ]);
    }
}
