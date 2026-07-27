<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('payment_types')->insert([
            [
                'name' => 'Destaque de Partida',
                'slug' => 'featured_match',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Taxa de Reserva',
                'slug' => 'booking_fee',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Assinatura Arena',
                'slug' => 'arena_subscription',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
