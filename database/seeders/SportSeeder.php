<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('sports')->insert([
            [
                'name' => 'Vôlei de Praia',
                'icon' => 'volleyball',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Vôlei de Quadra',
                'icon' => 'volleyball',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Futsal',
                'icon' => 'football',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Futvôlei',
                'icon' => 'football',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Basquete',
                'icon' => 'basketball',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Beach Tennis',
                'icon' => 'tennis',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Futebol',
                'icon' => 'football',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
