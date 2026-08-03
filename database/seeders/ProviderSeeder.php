<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('providers')->insert([
            [
                'name' => 'Google',
                'slug' => 'google',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
