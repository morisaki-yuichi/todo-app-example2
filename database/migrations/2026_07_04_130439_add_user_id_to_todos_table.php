<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 稼働中テーブルへの user_id 追加。既存データを壊さない3段階:
     *   1. まずnullableで列を追加(既存行はNULLで生き残る)
     *   2. 既存行を「持ち主なしTODO」の引き取り先(先頭ユーザー=デモ)に紐付ける
     *   3. 全行が値を持ったので NOT NULL + 外部キー制約を付ける
     * いきなりNOT NULLで追加すると、既存行が値を持てずマイグレーションが失敗する。
     */
    public function up(): void
    {
        // 1. nullableで追加
        Schema::table('todos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id');
        });

        // 2. 既存行のデータ移行(所有者がいなかったTODOを先頭ユーザーに引き取らせる)
        $ownerId = User::orderBy('id')->value('id');
        if ($ownerId !== null) {
            DB::table('todos')->whereNull('user_id')->update(['user_id' => $ownerId]);
        }

        // 3. NOT NULL化+外部キー制約(ユーザー削除時はそのTODOも消す)
        Schema::table('todos', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
