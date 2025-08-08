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
        
        //-------------------------------------//
        // IT Team
        //-------------------------------------//
        
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

        // Crear usuario 2: Panel User
        $user2 = new User();
        $user2->name = 'Felipe de Jesús Lazaro Sánchez';
        $user2->email = 'fls@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Fls123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        //-------------------------------------//
        // Stakeholders
        //-------------------------------------//

        // Crear usuario 3: Panel User
        $user2 = new User();
        $user2->name = 'Gabriel Holschneider Osuna';
        $user2->email = 'gho@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Gho123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 4: Panel User
        $user2 = new User();
        $user2->name = 'Mauricio Esquino Urdaneta';
        $user2->email = 'me@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Meu123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 5: Panel User
        $user2 = new User();
        $user2->name = 'Crisoforo Lozano Luna';
        $user2->email = 'cll@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Cll123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        //-------------------------------------//
        // Directors
        //-------------------------------------//

        // Crear usuario 6: Panel User
        $user2 = new User();
        $user2->name = 'Francisco Teodoro Oliveros';
        $user2->email = 'fog@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Fto123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 7: Panel User
        $user2 = new User();
        $user2->name = 'Gonzalo García Septien';
        $user2->email = 'gg@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Ggs123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        //-------------------------------------//
        // Sales Team
        //-------------------------------------//

        // Crear usuario 9: Panel User
        $user2 = new User();
        $user2->name = 'Rodrigo Manuel Gutiérrez';
        $user2->email = 'rgc@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Rmg123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 16: Panel User
        $user2 = new User();
        $user2->name = 'Vicente Tames';
        $user2->email = 'vta@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Vta123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        //-------------------------------------//
        // Underwritten Team
        //-------------------------------------//

        // Crear usuario 10: Panel User
        $user2 = new User();
        $user2->name = 'Brayan Pelcastre Jordan';
        $user2->email = 'bpj@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Bpj123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 14: Panel User
        $user2 = new User();
        $user2->name = 'Edgar Iván Figueroa';
        $user2->email = 'efe@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Eif123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 15: Panel User
        $user2 = new User();
        $user2->name = 'Vanessa Martínez González';
        $user2->email = 'vmg@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Vmg123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 28: Panel User
        $user2 = new User();
        $user2->name = 'Noemi Viveros Bernal';
        $user2->email = 'noemi.viveros@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Nvb123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 28: Panel User
        $user2 = new User();
        $user2->name = 'Arturo Ramírez Sánchez Villar';
        $user2->email = 'a.ramirez@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Ars123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 28: Panel User
        $user2 = new User();
        $user2->name = 'Sandra Geraldine Castillo Dorantes';
        $user2->email = 's.castillo@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Sgc123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 28: Panel User
        $user2 = new User();
        $user2->name = 'Carlos Eduardo Lara Uribe';
        $user2->email = 'c.lara@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Cel123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 28: Panel User
        $user2 = new User();
        $user2->name = 'Elizabeth García Cruz';
        $user2->email = 'elizabeth.garcia@rainmakergroup.com ';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Egc123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        //-------------------------------------//
        // Operations Team
        //-------------------------------------//

        // Crear usuario 11: Panel User
        $user2 = new User();
        $user2->name = 'José Luis Márquez';
        $user2->email = 'jmf@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Jlm123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 17: Panel User
        $user2 = new User();
        $user2->name = 'Alan Morales';
        $user2->email = 'amc@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Amc123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 18: Panel User
        $user2 = new User();
        $user2->name = 'Ana Sandoval Flores';
        $user2->email = 'asf@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Asf123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 19: Panel User
        $user2 = new User();
        $user2->name = 'Luis Antonio Segura Villanueva';
        $user2->email = 'lsv@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Lsv123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 19: Panel User
        $user2 = new User();
        $user2->name = 'Luis Antonio Segura Villanueva';
        $user2->email = 'lsv@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Lsv123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        //-------------------------------------//
        // Administration Barbados Team
        //-------------------------------------//

        // Crear usuario 12: Panel User
        $user2 = new User();
        $user2->name = 'Carolyn Humphrey';
        $user2->email = 'ch@integritymanagers.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Ch123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 13: Panel User
        $user2 = new User();
        $user2->name = 'Kira Almendra Gonzalez Tello';
        $user2->email = 'ago@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Kagt123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 27: Panel User
        $user2 = new User();
        $user2->name = 'Maria Fernanda Romero Valdovinos';
        $user2->email = 'mrv@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Mrv123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 20: Panel User
        $user2 = new User();
        $user2->name = 'Marcia Barnard';
        $user2->email = 'mc@integritymanagers.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Mb123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 21: Panel User
        $user2 = new User();
        $user2->name = 'Esther Reyes';
        $user2->email = 'er@integritymanagers.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Er123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 22: Panel User
        $user2 = new User();
        $user2->name = 'Kristy-Ann King';
        $user2->email = 'kak@integritymanagers.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Kak123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 23: Panel User
        $user2 = new User();
        $user2->name = 'Maria Kirton';
        $user2->email = 'mb@integritymanagers.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Mk123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 24: Panel User
        $user2 = new User();
        $user2->name = 'Micah Sealy';
        $user2->email = 'ms@integritymanagers.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Ms123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 25: Panel User
        $user2 = new User();
        $user2->name = 'Shelley Cadogan';
        $user2->email = 'sc@integritymanagers.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Sc123'; // Solo para desarrollo, sin hash
        $user2->remember_token = Str::random(10);
        $user2->created_at = Carbon::now();
        $user2->updated_at = Carbon::now();
        $user2->save();

        // Crear usuario 28: Panel User
        $user2 = new User();
        $user2->name = 'Noemi Viveros Bernal';
        $user2->email = 'noemi.viveros@rainmakergroup.com';
        $user2->email_verified_at = Carbon::now();
        $user2->password = 'Nvb123'; // Solo para desarrollo, sin hash
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
