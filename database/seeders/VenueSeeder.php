<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quadraPracaJuventude = Venue::factory()->create([
            'name' => 'Quadra Praça da Juventude',
            'city' => 'Ribeira do Pombal',
            'state' => 'BA',
            'neighborhood' => 'Centro',
            'latitude' => -10.83839578,
            'longitude' => -38.54361091,
            'verified' => true,
            'featured' => true,
            'address' => 'R. Princesa Isabel',
        ]);

        $quadraPracaJuventude->sports()->attach([2, 3, 5]);

        $quadraAreiaPracaJuventude = Venue::factory()->create([
            'name' => 'Quadra de Areia Praça da Juventude',
            'city' => 'Ribeira do Pombal',
            'state' => 'BA',
            'neighborhood' => 'Centro',
            'latitude' => -10.83839578,
            'longitude' => -38.54361091,
            'verified' => true,
            'featured' => true,
            'address' => 'R. Princesa Isabel',
        ]);

        $quadraAreiaPracaJuventude->sports()->attach([1, 4]);

        $quadraAreiaPrefeitura = Venue::factory()->create([
            'name' => 'Quadra Areia Prefeitura (Areninha)',
            'city' => 'Ribeira do Pombal',
            'state' => 'BA',
            'neighborhood' => 'Centro',
            'latitude' => -10.845384950,
            'longitude' => -38.54198092,
            'verified' => true,
            'featured' => true,
            'address' => 'Praça José Domingos',
        ]);

        $quadraAreiaPrefeitura->sports()->attach([1, 4]);
    }
}
