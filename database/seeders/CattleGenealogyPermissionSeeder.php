<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CattleGenealogyPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate([
            'name' => 'Administrador',
            'guard_name' => 'web',
        ]);

        $permissions = [
            'admin.cattle-genealogy.index' => 'Ver genealogía del ganado',
            'admin.cattle-genealogy.store' => 'Crear genealogía del ganado',
            'admin.cattle-genealogy.update' => 'Actualizar genealogía del ganado',
            'admin.cattle-genealogy.destroy' => 'Eliminar genealogía del ganado',
        ];

        foreach ($permissions as $name => $description) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['description' => $description]
            );

            $role->givePermissionTo($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
