<?php

namespace Database\Factories;

use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * テスト用のTodoを組み立てる工場。
 *
 * @extends Factory<Todo>
 */
class TodoFactory extends Factory
{
    protected $model = Todo::class;

    /**
     * 既定値。テストごとに ->state([...]) や ->completed() で上書きする。
     */
    public function definition(): array
    {
        return [
            'title' => fake()->realText(20),
            'description' => fake()->optional()->realText(50),
            'due_date' => null,
            'completed' => false,
        ];
    }

    /**
     * 完了済みの状態(状態メソッド)。テストで ->completed() と書ける。
     */
    public function completed(): static
    {
        return $this->state(['completed' => true]);
    }

    /**
     * 期限切れの状態(過去日+未完了)。
     */
    public function overdue(): static
    {
        return $this->state([
            'due_date' => now()->subDay()->toDateString(),
            'completed' => false,
        ]);
    }
}
