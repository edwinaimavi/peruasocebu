<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('carga el tema corporativo en el dashboard administrativo', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee('PERU ASOCEBU')
        ->assertSee('css/admin-modern.css')
        ->assertSee('dashboard-welcome', false);
});

it('mantiene operativo el módulo de usuarios con el nuevo encabezado', function () {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'admin.users.index']);
    $user->givePermissionTo($permission);

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Nuevo Usuario')
        ->assertSee('tableUser')
        ->assertSee('css/admin-modern.css');
});
