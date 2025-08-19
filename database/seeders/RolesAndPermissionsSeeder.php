<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia caché antes de tocar permisos/roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        // 1) Crear/asegurar roles
        $superAdmin = Role::firstOrCreate(['name' => config('filament-shield.super_admin.name', 'super_admin'), 'guard_name' => $guard]);
        $admin      = Role::firstOrCreate(['name' => 'admin',       'guard_name' => $guard]);
        $staff      = Role::firstOrCreate(['name' => 'staff',       'guard_name' => $guard]);
        $user       = Role::firstOrCreate(['name' => 'user',        'guard_name' => $guard]);
        $panelUser  = Role::firstOrCreate(['name' => 'panel_user',  'guard_name' => $guard]); // sin permisos

        // 2) Permisos generados por Shield (mismo guard)
        $all = Permission::where('guard_name', $guard)->get();

        // 3) Asignaciones

        // super_admin: todos
        $superAdmin->syncPermissions($all);

        // admin: todo excepto acciones destructivas/restauración
        $adminDeniedPrefixes = [
            'delete_any_', 'delete_', 'force_delete_any_', 'force_delete_', 'restore_any_', 'restore_',
        ];
        $adminPerms = $all->reject(function ($perm) use ($adminDeniedPrefixes) {
            return Str::startsWith($perm->name, $adminDeniedPrefixes);
        });
        $admin->syncPermissions($adminPerms->values());

        // staff: CRUD básico + view/view_any (páginas/widgets también inician con view_)
        $staffAllowedPrefixes = ['view_', 'view_any_', 'create_', 'update_'];
        $staffPerms = $all->filter(function ($perm) use ($staffAllowedPrefixes) {
            return Str::startsWith($perm->name, $staffAllowedPrefixes);
        });
        $staff->syncPermissions($staffPerms->values());

        // user: solo lectura
        $userAllowedPrefixes = ['view_', 'view_any_'];
        $userPerms = $all->filter(function ($perm) use ($userAllowedPrefixes) {
            return Str::startsWith($perm->name, $userAllowedPrefixes);
        });
        $user->syncPermissions($userPerms->values());

        // panel_user: sin permisos (intencional)

        // 4) Limpia caché al final
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
