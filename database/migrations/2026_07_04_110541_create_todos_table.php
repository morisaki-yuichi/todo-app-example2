<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);              // 必須(NOT NULLがデフォルト)・最大100文字
            $table->text('description')->nullable();   // 任意項目なのでNULLを許可
            $table->boolean('completed')->default(false); // 新規TODOは未完了から始まる
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
