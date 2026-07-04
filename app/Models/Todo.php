<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    /**
     * ユーザー入力からの一括代入(マスアサインメント)を許可するカラム。
     * ここに無いカラムはcreate()/update()の配列指定で書き込めない(安全側に倒す)。
     */
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'completed',
    ];

    /**
     * DBの値をPHPの型に変換する設定。
     * completedはMySQL上ではtinyint(0/1)だが、PHP側では常にtrue/falseとして扱う。
     */
    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'due_date' => 'date', // DBのdate文字列をCarbon(日付オブジェクト)として扱う
        ];
    }

    /**
     * 期限切れか?(期限があり・未完了で・期限日が昨日以前)
     *
     * 判定ルールをモデルに置くことで、複数のビューから同じ基準で使える。
     * 「今日が期限」はまだ間に合うので期限切れに含めない(lt = より前)。
     */
    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && ! $this->completed
            && $this->due_date->lt(today());
    }
}
