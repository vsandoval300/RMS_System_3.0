<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionsType;

class TransactionsTypesSeeder extends Seeder
{
    public function run(): void
    {
        // ‼️  Estos valores DEBEN coincidir con los permitidos en el enum
        $tipos = [
            ['description' => 'Premium'],
            ['description' => 'Claims'],
            ['description' => 'Claims Reserve'],
        ];

        TransactionsType::insert($tipos);
    }
}
