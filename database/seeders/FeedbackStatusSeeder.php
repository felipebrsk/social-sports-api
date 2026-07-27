<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('feedback_statuses')->insert([
            [
                'name' => 'Pendente',
                'slug' => 'pending',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Em Análise',
                'slug' => 'in_review',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Resolvido / Concluído',
                'slug' => 'resolved',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Recusado / Ignorado',
                'slug' => 'rejected',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
