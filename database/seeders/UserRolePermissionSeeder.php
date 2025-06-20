<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Create permissions
        Permission::create(['name' => 'view role']);
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'view permission']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'update permission']);
        Permission::create(['name' => 'delete permission']);

        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);

        Permission::create(['name' => 'view client']);
        Permission::create(['name' => 'create client']);
        Permission::create(['name' => 'update client']);
        Permission::create(['name' => 'delete client']);
        
        //Create roles
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $staffRole = Role::create(['name' => 'staff']);
        $userRole = Role::create(['name' => 'user']);

        //Let gives all permissions to super-admin
        $allPermissionNames = Permission::pluck('name')->toArray();
        $superAdminRole->givePermissionTo($allPermissionNames);

        //Let's give few permissions to admin role
        $adminRole->givePermissionTo(['create role', 'view role', 'update role']);
        $adminRole->givePermissionTo(['create permission','view permission']);
        $adminRole->givePermissionTo(['create user', 'view user', 'update user']);
        $adminRole->givePermissionTo(['create client', 'view client', 'update client']);

        //Let's create user and assign role to it.
        $superAdminUser = User::firstOrCreate([
            'email' => 'fls@rainmakergroup.com',
        ],[
            'name' => 'Felipe de Jesús',
            'surnme' => 'L{azaro Sánchez',
            'email' => 'fls@rainmakergroup.com',
            'password' => Hash::make('TBsYTDj1Bb')
        ]);

        $superAdminUser->assignRole($superAdminRole);

        $adminUser = User::firstOrCreate([
            'email' => 'dvm@rainmakergroup.com '
        ], [  
            'name' => 'Maria Dolores',
            'surname' => 'Velazquez Morales',
            'email' => 'dvm@rainmakergroup.com ',
            'password' => Hash::make ('12345678'),
        ]);

        $adminUser->assignRole($adminRole);


        $staffUser = User::firstOrCreate([
                    'email' => 'ricmtra@hotmail.com',
                ], [
                    'name' => 'Staff',
                    'email' => 'ricmtra@hotmail.com',
                    'password' => Hash::make('12345678'),
                ]);

        $staffUser->assignRole($staffRole);
    }
}
