<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('feedback_categories')->insert([
            [
                'name' => 'Sugestão',
                'slug' => 'suggestion',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Reclamação',
                'slug' => 'complaint',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Problema Técnico / Bug',
                'slug' => 'bug_report',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Solicitar Novo Esporte',
                'slug' => 'request_sport',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Solicitar Nova Quadra',
                'slug' => 'request_venue',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Outros',
                'slug' => 'other',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
