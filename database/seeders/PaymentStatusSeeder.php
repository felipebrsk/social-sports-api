<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('payment_statuses')->insert([
            [
                'name' => 'Aguardando Pagamento',
                'slug' => 'pending',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Pago',
                'slug' => 'paid',
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
                'name' => 'Reembolsado',
                'slug' => 'refunded',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
