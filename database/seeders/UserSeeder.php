<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 0) Limpiar cache de permisos/roles
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1) Vaciar tabla users y pivotes de Spatie
        Schema::disableForeignKeyConstraints();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // 2) Roles (guard web)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $panelUser  = Role::firstOrCreate(['name' => 'panel_user',  'guard_name' => 'web']);
        $roles = [
            'super_admin' => $superAdmin,
            'panel_user'  => $panelUser,
        ];

        // 3) Usuarios (password en claro aquí, se hashea al crear)
        $users = [
            // --- IT Team ---
            ['name' => 'Víctor Sandoval', 'email' => 'vsa@rainmakergroup.com', 'password' => 'Yahoo#03', 'role' => 'super_admin', 'department_id' => '13', 'position_id' => '53'],
            ['name' => 'Dolores Velazquez', 'email' => 'dvm@rainmakergroup.com', 'password' => 'Mary123', 'role' => 'panel_user', 'department_id' => '13', 'position_id' => '54'],
            ['name' => 'Felipe de Jesús Lazaro Sánchez', 'email' => 'fls@rainmakergroup.com', 'password' => 'Fls123', 'role' => 'panel_user', 'department_id' => '13', 'position_id' => '55'],

            // --- Stakeholders ---
            ['name' => 'Gabriel Holschneider Osuna', 'email' => 'gho@rainmakergroup.com', 'password' => 'Gho123', 'role' => 'panel_user', 'department_id' => '12', 'position_id' => '56'],
            ['name' => 'Mauricio Esquino Urdaneta', 'email' => 'me@rainmakergroup.com', 'password' => 'Meu123', 'role' => 'panel_user', 'department_id' => '12', 'position_id' => '56'],
            ['name' => 'Crisoforo Lozano Luna', 'email' => 'cll@rainmakergroup.com', 'password' => 'Cll123', 'role' => 'panel_user', 'department_id' => '12', 'position_id' => '56'],

            // --- Directors ---
            ['name' => 'Francisco Teodoro Oliveros', 'email' => 'fog@rainmakergroup.com', 'password' => 'Fto123', 'role' => 'panel_user', 'department_id' => '5', 'position_id' => '40'],
            ['name' => 'Gonzalo García Septien', 'email' => 'gg@rainmakergroup.com', 'password' => 'Ggs123', 'role' => 'panel_user', 'department_id' => '5', 'position_id' => '41'],

            // --- Sales Team ---
            ['name' => 'Rodrigo Manuel Gutiérrez', 'email' => 'rgc@rainmakergroup.com', 'password' => 'Rmg123', 'role' => 'panel_user', 'department_id' => '3', 'position_id' => '50'],
            ['name' => 'Vicente Tames', 'email' => 'vta@rainmakergroup.com', 'password' => 'Vta123', 'role' => 'panel_user', 'department_id' => '3', 'position_id' => '50'],

            // --- 1 Team ---
            ['name' => 'Brayan Pelcastre Jordan', 'email' => 'bpj@rainmakergroup.com', 'password' => 'Bpj123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '19'],
            ['name' => 'Edgar Iván Figueroa', 'email' => 'efe@rainmakergroup.com', 'password' => 'Eif123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '10'],
            ['name' => 'Vanessa Martínez González', 'email' => 'vmg@rainmakergroup.com', 'password' => 'Vmg123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '10'],
            ['name' => 'Noemi Viveros Bernal', 'email' => 'noemi.viveros@rainmakergroup.com', 'password' => 'Nvb123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '9'],
            ['name' => 'Arturo Ramírez Sánchez Villar', 'email' => 'a.ramirez@rainmakergroup.com', 'password' => 'Ars123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '9'],
            ['name' => 'Sandra Geraldine Castillo Dorantes', 'email' => 's.castillo@rainmakergroup.com', 'password' => 'Sgc123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '9'],
            ['name' => 'Carlos Eduardo Lara Uribe', 'email' => 'c.lara@rainmakergroup.com', 'password' => 'Cel123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '9'],
            ['name' => 'Elizabeth García Cruz', 'email' => 'elizabeth.garcia@rainmakergroup.com', 'password' => 'Egc123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '9'],

            // --- 2 Team ---
            ['name' => 'José Luis Márquez', 'email' => 'jmf@rainmakergroup.com', 'password' => 'Jlm123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '57'],
            ['name' => 'Alan Morales', 'email' => 'amc@rainmakergroup.com', 'password' => 'Amc123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '30'],
            ['name' => 'Ana Sandoval Flores', 'email' => 'asf@rainmakergroup.com', 'password' => 'Asf123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '30'],
            ['name' => 'Luis Antonio Segura Villanueva', 'email' => 'lsv@rainmakergroup.com', 'password' => 'Lsv123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '30'],

            // --- Administration Barbados Team ---
            ['name' => 'Carolyn Humphrey', 'email' => 'ch@integritymanagers.com', 'password' => 'Ch123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '32'],
            ['name' => 'Kira Almendra Gonzalez Tello', 'email' => 'ago@rainmakergroup.com', 'password' => 'Kagt123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '37'],
            ['name' => 'Maria Fernanda Romero Valdovinos', 'email' => 'mrv@rainmakergroup.com', 'password' => 'Mrv123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '16'],
            ['name' => 'Marcia Barnard', 'email' => 'mc@integritymanagers.com', 'password' => 'Mb123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '44'],
            ['name' => 'Esther Reyes', 'email' => 'er@integritymanagers.com', 'password' => 'Er123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '12'],
            ['name' => 'Kristy-Ann King', 'email' => 'kak@integritymanagers.com', 'password' => 'Kak123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '12'],
            ['name' => 'Maria Kirton', 'email' => 'mb@integritymanagers.com', 'password' => 'Mk123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '12'],
            ['name' => 'Micah Sealy', 'email' => 'ms@integritymanagers.com', 'password' => 'Ms123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '12'],
            ['name' => 'Shelley Cadogan', 'email' => 'sc@integritymanagers.com', 'password' => 'Sc123', 'role' => 'panel_user', 'department_id' => '6', 'position_id' => '31'],
        ];

        // 4) Crear usuarios y asignar roles
        foreach ($users as $u) {
            $user = User::create([
                'name' => $u['name'],
                'email' => trim($u['email']),
                'password' => Hash::make($u['password']), // siempre hash
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                
                'department_id'     => (int) $u['department_id'],
                'position_id'       => (int) $u['position_id'],
            ]);

            $roleKey = $u['role'] ?? 'panel_user';
            if (isset($roles[$roleKey])) {
                $user->assignRole($roles[$roleKey]);
            }
        }

        // 5) Otorgar todos los permisos existentes al rol super_admin
        $superAdmin->syncPermissions(Permission::pluck('name')->toArray());
    }
}
