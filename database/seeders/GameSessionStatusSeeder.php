<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSessionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('game_session_statuses')->insert([
            [
                'name' => 'Aberto',
                'slug' => 'open',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Lotado',
                'slug' => 'full',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Cancelado',
                'slug' => 'canceled',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Concluído',
                'slug' => 'finished',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
