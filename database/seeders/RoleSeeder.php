<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('roles')->insert([
            [
                'name' => 'system_admin',
                'description' => 'Administrador global da plataforma',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'owner',
                'description' => 'Dono ou responsável principal por uma quadra/arena',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'manager',
                'description' => 'Gerente de quadra com permissão para gerenciar agenda',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'team_captain',
                'description' => 'Capitão ou criador de um time',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'team_member',
                'description' => 'Membro/Atleta de um time',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
