<?php

use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['app.url' => 'http://localhost']);
    URL::forceRootUrl('http://localhost');
    $this->withServerVariables([
        'HTTP_HOST' => 'localhost',
        'SERVER_NAME' => 'localhost',
    ]);

    $this->user = User::factory()->create();

    collect([
        'admin.owners.index',
        'admin.owners.store',
        'admin.owners.update',
        'admin.owners.destroy',
    ])->each(function (string $permission) {
        Permission::create(['name' => $permission]);
    });

    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);
});

it('muestra el módulo de propietarios', function () {
    $this->get(route('admin.owners.index'))
        ->assertOk()
        ->assertSee('Propietarios / Dueños')
        ->assertSee('Nuevo Propietario')
        ->assertSee('css/admin-modern.css');
});

it('devuelve el listado para DataTable', function () {
    Owner::create([
        'owner_type' => 'company',
        'document_type' => 'RUC',
        'document_number' => '20123456789',
        'full_name' => 'Ana Torres',
        'business_name' => 'Ganadería Los Andes SAC',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.owners.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.display_name', 'Ganadería Los Andes SAC')
        ->assertSee('badge-success', false)
        ->assertSee('Empresa', false);
});

it('crea y actualiza un propietario', function () {
    $this->post(route('admin.owners.store'), [
        'owner_type' => 'person',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'full_name' => 'Carlos Mendoza',
        'email' => 'carlos@example.test',
        'status' => 'active',
    ])->assertOk()
        ->assertJson(['message' => 'Propietario registrado correctamente.']);

    $owner = Owner::firstOrFail();

    $this->put(route('admin.owners.update', $owner), [
        'owner_type' => 'company',
        'document_type' => 'RUC',
        'document_number' => '20123456789',
        'full_name' => 'Carlos Mendoza',
        'business_name' => 'Mendoza Ganadera SAC',
        'status' => 'inactive',
    ])->assertOk()
        ->assertJson(['message' => 'Propietario actualizado correctamente.']);

    $owner->refresh();

    expect($owner->owner_type)->toBe('company')
        ->and($owner->business_name)->toBe('Mendoza Ganadera SAC')
        ->and($owner->status)->toBe('inactive');
});

it('devuelve el detalle completo y elimina mediante soft delete', function () {
    $owner = Owner::create([
        'owner_type' => 'person',
        'document_type' => 'PASSPORT',
        'document_number' => 'PE123456',
        'full_name' => 'María López',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.owners.show', $owner))
        ->assertOk()
        ->assertJsonPath('owner.full_name', 'María López')
        ->assertJsonPath('owner.document_type_label', 'Pasaporte')
        ->assertJsonPath('owner.status_label', 'Activo');

    $this->delete(route('admin.owners.destroy', $owner))
        ->assertOk()
        ->assertJson(['message' => 'Propietario eliminado correctamente.']);

    $this->assertSoftDeleted('owners', ['id' => $owner->id]);
});

it('valida campos obligatorios, valores permitidos y correo', function () {
    $this->postJson(route('admin.owners.store'), [
        'owner_type' => 'invalid',
        'document_type' => 'INVALID',
        'full_name' => '',
        'email' => 'correo-invalido',
        'status' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors([
            'owner_type',
            'document_type',
            'full_name',
            'email',
            'status',
        ]);
});
