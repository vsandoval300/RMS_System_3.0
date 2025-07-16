<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario 1: Super Admin
        $user1 = new User();
        $user1->name = 'Víctor Sandoval';
        $user1->email = 'vsa@rainmakergroup.com';
        $user1->email_verified_at = Carbon::now();
        $user1->password = 'Yahoo#03'; // Solo para desarrollo, sin hash
        $user1->remember_token = Str::random(10);
        $user1->created_at = Carbon::now();
        $user1->updated_at = Carbon::now();
        $user1->save();

        // Crear usuario 2: Panel User
        $user2 = new User();
        $user2->name = 'Dolores Velazquez';
        $user2->email = 'dvm@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Mary123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Buscar o crear roles
        $superAdminRole = Role::findOrCreate('super_admin', 'web');
        $panelUserRole = Role::findOrCreate('panel_user', 'web');

        // Asignar roles
        $user1->assignRole($superAdminRole);
        $user2->assignRole($panelUserRole);

        // Dar todos los permisos al rol super_admin
        $allPermissions = Permission::pluck('name');
        $superAdminRole->syncPermissions($allPermissions);
    }
}
