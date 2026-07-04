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
        // 認証機能は本プロジェクトのスコープ外のため、Userのダミーは作らない
        $this->call([
            TodoSeeder::class,
        ]);
    }
}
