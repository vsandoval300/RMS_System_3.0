<?php

namespace Database\Seeders;

use App\Models\board;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BoardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
       
        for ($i = 1; $i <= 92; $i++) {
            Board::create([
                'index' => $i,
                // Otros campos se dejan vacíos o con valores por defecto si es necesario
            ]);
        }
    }
}
