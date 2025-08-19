<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ShieldGenerateSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('Generating Filament Shield permissions...');

        // ⚠️ Cambia 'admin' si tu panel usa otro id (por ejemplo 'app' o 'staff')
        $panelId = 'admin';

        try {
            $exit = Artisan::call('shield:generate', [
                '--all'            => true,
                '--panel'          => $panelId,   // ← clave para evitar el prompt
                '--no-interaction' => true,
                '--quiet'          => true,
            ]);
            $this->command?->info("Shield generation done. Exit code: {$exit}");
        } catch (\Throwable $e) {
            $this->command?->error('Shield generation failed: '.$e->getMessage());
            throw $e;
        }
    }
}
