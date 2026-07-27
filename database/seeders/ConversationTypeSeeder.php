<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('conversation_types')->insert([
            [
                'name' => 'Grupo do Jogo',
                'slug' => 'match_group',
                'description' => 'Chat da sessão de jogo para os participantes aprovados.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Conversa Direta',
                'slug' => 'direct_chat',
                'description' => 'Chat privado entre dois usuários.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
