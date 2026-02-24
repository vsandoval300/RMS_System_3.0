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
            ['name' => 'Víctor Manuel Sandoval Arias', 'initials' => 'VMSA', 'email' => 'vsa@rainmakergroup.com', 'password' => 'Yahoo#03', 'role' => 'super_admin', 'department_id' => '13', 'position_id' => '14'],
            ['name' => 'María Dolores Velazquez Morales', 'initials' => 'MDVM', 'email' => 'dvm@rainmakergroup.com', 'password' => 'Mary123', 'role' => 'super_admin', 'department_id' => '13', 'position_id' => '15'],
            ['name' => 'Felipe de Jesús Lazaro Sánchez', 'initials' => 'FJLS', 'email' => 'fls@rainmakergroup.com', 'password' => 'Fls123', 'role' => 'super_admin', 'department_id' => '13', 'position_id' => '16'],

            // --- Stakeholders ---
            ['name' => 'Gabriel Holschneider Osuna', 'initials' => 'GHO', 'email' => 'gho@rainmakergroup.com', 'password' => 'Gho123', 'role' => 'panel_user', 'department_id' => '12', 'position_id' => '17'],
            ['name' => 'Mauricio Salvador Esquino Urdaneta', 'initials' => 'MSEU', 'email' => 'me@rainmakergroup.com', 'password' => 'Meu123', 'role' => 'panel_user', 'department_id' => '12', 'position_id' => '17'],
            ['name' => 'Crisoforo Lozano Luna', 'initials' => 'CLL', 'email' => 'c.lozano@rainmakergroup.com', 'password' => 'Cll123', 'role' => 'panel_user', 'department_id' => '12', 'position_id' => '17'],

            // --- General Management ---
            ['name' => 'Francisco Teodoro Oliveros', 'initials' => 'FTO', 'email' => 'f.oliveros@rainmakergroup.com', 'password' => 'Fto123', 'role' => 'panel_user', 'department_id' => '15', 'position_id' => '9'],
            ['name' => 'Gonzalo García Septien', 'initials' => 'GGS', 'email' => 'gg@rainmakergroup.com', 'password' => 'Ggs123', 'role' => 'panel_user', 'department_id' => '15', 'position_id' => '10'],
            ['name' => 'Ana Valeria Mejia Cortés', 'initials' => 'AVMC', 'email' => 'a.mejia@rainmakergroup.com', 'password' => 'Asc123', 'role' => 'panel_user', 'department_id' => '15', 'position_id' => '11'],

            // --- Commercial Team ---
            ['name' => 'Rodrigo Manuel Gutiérrez', 'initials' => 'RMG', 'email' => 'r.gutierrez@rainmakergroup.com', 'password' => 'Rmg123', 'role' => 'panel_user', 'department_id' => '3', 'position_id' => '12'],
            ['name' => 'Vicente Tames', 'initials' => 'VT','email' => 'v.tames@rainmakergroup.com', 'password' => 'Vta123', 'role' => 'panel_user', 'department_id' => '3', 'position_id' => '13'],
            ['name' => 'Kimberly Pamela Mejia Cortés', 'initials' => 'KPMC', 'email' => 'k.mejia@rainmakergroup.com', 'password' => 'Kpm123', 'role' => 'panel_user', 'department_id' => '3', 'position_id' => '13'],

            // --- Underwritten Team ---
            ['name' => 'Brayan Pelcastre Jordan', 'initials' => 'BPJ', 'email' => 'bpj@rainmakergroup.com', 'password' => 'Bpj123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '4'],
            ['name' => 'Edgar Iván Figueroa', 'initials' => 'EIF', 'email' => 'efe@rainmakergroup.com', 'password' => 'Eif123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '2'],
            ['name' => 'Vanessa Martínez González', 'initials' => 'VMG', 'email' => 'vmg@rainmakergroup.com', 'password' => 'Vmg123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '2'],
            ['name' => 'Noemi Viveros Bernal', 'initials' => 'NVB', 'email' => 'nvb@rainmakergroup.com', 'password' => 'Nvb123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '1'],
            ['name' => 'Arturo Ramírez Sánchez Villar', 'initials' => 'ARSV', 'email' => 'arr@rainmakergroup.com', 'password' => 'Ars123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '2'],
            ['name' => 'Sandra Geraldine Castillo Dorantes', 'initials' => 'SGCD', 'email' => 'sc@rainmakergroup.com', 'password' => 'Sgc123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '1'],
            ['name' => 'Carlos Eduardo Lara Uribe', 'initials' => 'CELU', 'email' => 'clu@rainmakergroup.com', 'password' => 'Cel123', 'role' => 'panel_user', 'department_id' => '1', 'position_id' => '1'],
            

            // --- Operations Team ---
            ['name' => 'José Luis Márquez', 'initials' => 'JLM', 'email' => 'l.marquez@rainmakergroup.com', 'password' => 'Jlm123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '18'],
            ['name' => 'Alan Morales', 'initials' => 'AM', 'email' => 'a.morales@rainmakergroup.com', 'password' => 'Amc123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '8'],
            ['name' => 'Ana Rosa Sandoval Flores', 'initials' => 'ARSF', 'email' => 'a.sandoval@rainmakergroup.com', 'password' => 'Asf123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '8'],
            ['name' => 'Luis Antonio Segura Villanueva', 'initials' => 'LASV', 'email' => 'l.segura@rainmakergroup.com', 'password' => 'Lsv123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '8'],
            ['name' => 'Diego Sánchez', 'initials' => 'DS', 'email' => 'd.sanchez@rainmakergroup.com', 'password' => 'Dss123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '8'],
            ['name' => 'Lizzeth Abigail Martinez', 'initials' => 'LAM', 'email' => 'la.martinez@rainmakergroup.com', 'password' => 'Lmm123', 'role' => 'panel_user', 'department_id' => '2', 'position_id' => '8'],

            // --- Country Management ---
            ['name' => 'Carolyn Humphrey', 'initials' => 'CH', 'email' => 'c.humphrey@integritymanagers.com', 'password' => 'Ch123', 'role' => 'panel_user', 'department_id' => '16', 'position_id' => '19'],
            ['name' => 'Marcia Barnard', 'initials' => 'MB', 'email' => 'm.barnard@integritymanagers.com', 'password' => 'Mb123', 'role' => 'panel_user', 'department_id' => '16', 'position_id' => '20'],
            ['name' => 'Maria Kirton', 'initials' => 'MK', 'email' => 'm.kirton@integritymanagers.com', 'password' => 'Mk123', 'role' => 'panel_user', 'department_id' => '16', 'position_id' => '20'],

            // --- Corporate Governance ---
            ['name' => 'Kira Almendra Gonzalez Tello', 'initials' => 'KAGT', 'email' => 'a.gonzalez@integritymanagers.com', 'password' => 'Kagt123', 'role' => 'panel_user', 'department_id' => '17', 'position_id' => '5'],
            ['name' => 'Maria Fernanda Romero Valdovinos', 'initials' => 'MFRV', 'email' => 'm.romero@rainmakergroup.com', 'password' => 'Mrv123', 'role' => 'panel_user', 'department_id' => '17', 'position_id' => '7'],
            ['name' => 'Esther Reyes', 'initials' => 'ER', 'email' => 'e.reyes@integritymanagers.com', 'password' => 'Er123', 'role' => 'panel_user', 'department_id' => '17', 'position_id' => '21'],
            ['name' => 'Kristy-Ann King', 'initials' => 'KK', 'email' => 'k.annking@integritymanagers.com', 'password' => 'Kak123', 'role' => 'panel_user', 'department_id' => '17', 'position_id' => '21'],
            ['name' => 'Micah Sealy', 'initials' => 'MS', 'email' => 'm.sealy@integritymanagers.com', 'password' => 'Ms123', 'role' => 'panel_user', 'department_id' => '17', 'position_id' => '21'],


            // --- Risk Area ---
            ['name' => 'Ana Mendez', 'initials' => 'AM', 'email' => 'ame@rainmakergroup.com', 'password' => 'Anm123', 'role' => 'panel_user', 'department_id' => '7', 'position_id' => '6'],
            ['name' => 'Diego Armando Letona', 'initials' => 'DAL', 'email' => 'all@rainmakergroup.com', 'password' => 'Lda123', 'role' => 'panel_user', 'department_id' => '7', 'position_id' => '3'],
            ['name' => 'Jorge Alejandro Giron', 'initials' => 'JAG', 'email' => 'jgb@rainmakergroup.com', 'password' => 'Gja123', 'role' => 'panel_user', 'department_id' => '7', 'position_id' => '22'],
            
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
        //$superAdmin->syncPermissions(Permission::pluck('name')->toArray());
    }
}
