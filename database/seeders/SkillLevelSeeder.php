<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('skill_levels')->insert([
            [
                'name' => 'Iniciante',
                'slug' => 'beginner',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Intermediário',
                'slug' => 'intermediate',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Avançado',
                'slug' => 'advanced',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Livre / Todos os Níveis',
                'slug' => 'all',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
