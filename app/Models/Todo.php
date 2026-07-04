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
}
