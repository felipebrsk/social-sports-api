<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSessionRequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('game_session_request_statuses')->insert([
            [
                'name' => 'Pendente',
                'slug' => 'pending',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Aprovado',
                'slug' => 'approved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Recusado',
                'slug' => 'rejected',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
