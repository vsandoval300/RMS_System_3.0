<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Str;

class UnderwriterBasicRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia caché en runtime para que Spatie vea los permisos recién generados por Shield
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';
        $roleName = 'underwriter_basic';

        // 1) Crear o actualizar el rol
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => $guard,
        ]);

        // 2) Permisos base: view + view_any para TODOS los permisos existentes
        // Shield típicamente genera: view_any_xxx y view_xxx
        $base = Permission::query()
            ->where('guard_name', $guard)
            ->where(function ($q) {
                $q->where('name', 'like', 'view_%')
                  ->orWhere('name', 'like', 'view_any_%');
            })
            ->pluck('name')
            ->all();

        // 3) Extra: permitir create en Business (y lo que quieras adicional)
        // Ajusta estos nombres si tus permisos custom difieren
        $resourceKeywords = [
            // Según tus cards visibles en screenshots
            'bank_account',
            'bank',
            'business',
            'business_doc_type',
            'client',
            'company',
            'document_type',
            'placement',        // placement_scheme
            'scheme',           // cost_scheme
            'country',
            'coverage',
            'currency',
            'director',
            'holding',
            'industry',
            'line_of_business',
            'manager',
            'operative_status',
            'partner_type',
            'partner',
            'producer',
            'region',
            'reinsurer_type',
            'reinsurer',
            'subregion',
            'transaction_log',
            'treaty',
            'user',
            // NOTA: Role lo dejo fuera porque en el screenshot no tiene create
        ];

        $extras = Permission::query()
            ->where('guard_name', $guard)
            ->get()
            ->filter(function (Permission $p) use ($resourceKeywords) {

                // Solo permisos de escritura (Create/Update)
                if (! Str::startsWith($p->name, ['create_', 'update_'])) {
                    return false;
                }

                $name = Str::lower($p->name);

                // match por keyword “contenida” para soportar nombres tipo placement::scheme
                foreach ($resourceKeywords as $kw) {
                    if (Str::contains($name, $kw)) {
                        return true;
                    }
                }

                return false;
            })
            ->pluck('name')
            ->values()
            ->all();

        // 4) Sync (idempotente)
        $role->syncPermissions(array_values(array_unique(array_merge($base, $extras))));

        $this->command?->info("Role '{$roleName}' synced with base view permissions + extras.");
    }
}
