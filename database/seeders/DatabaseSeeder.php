<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 写経者がすぐログインを試せるデモユーザー(パスワードは自動でハッシュ化される)。
        // updateOrCreateで再実行しても重複しない(emailがunique)
        User::updateOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'デモユーザー', 'password' => 'password'],
        );

        $this->call([
            TodoSeeder::class,
        ]);
    }
}
