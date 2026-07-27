<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('social_networks')->insert([
            [
                'name' => 'Instagram',
                'icon' => 'instagram',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Grupo do WhatsApp',
                'icon' => 'whatsapp',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'TikTok',
                'icon' => 'tiktok',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'YouTube',
                'icon' => 'youtube',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Website',
                'icon' => 'globe',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
