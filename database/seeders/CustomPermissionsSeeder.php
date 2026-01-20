<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CustomPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Business – acciones especiales
            'business.technical_result',
            'business.renewal',
            'business.add_transaction',
            'print_summary_business',
            'business.view_transactions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}

