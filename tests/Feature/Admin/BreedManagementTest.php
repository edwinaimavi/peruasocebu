<?php

use App\Models\Breed;
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
        'admin.breeds.index',
        'admin.breeds.store',
        'admin.breeds.update',
        'admin.breeds.destroy',
    ])->each(function (string $permission) {
        Permission::create(['name' => $permission]);
    });

    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);
});

it('muestra el módulo de razas', function () {
    $this->get(route('admin.breeds.index'))
        ->assertOk()
        ->assertSee('Razas de Ganado')
        ->assertSee('Nueva Raza')
        ->assertSee('tableBreed');
});

it('devuelve el listado para DataTable', function () {
    Breed::create([
        'name' => 'Brahman',
        'code' => 'BRH',
        'origin_country' => 'Estados Unidos',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.breeds.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.name', 'Brahman')
        ->assertSee('badge-success', false)
        ->assertSee('BRH', false);
});

it('crea y actualiza una raza normalizando el código', function () {
    $this->post(route('admin.breeds.store'), [
        'name' => 'Gyr',
        'code' => 'gyr',
        'origin_country' => 'India',
        'description' => 'Raza lechera cebuina.',
        'characteristics' => 'Rusticidad y aptitud lechera.',
        'status' => 'active',
    ])->assertOk()
        ->assertJson(['message' => 'Raza registrada correctamente.']);

    $breed = Breed::firstOrFail();

    expect($breed->code)->toBe('GYR');

    $this->put(route('admin.breeds.update', $breed), [
        'name' => 'Gyr Lechero',
        'code' => 'gyr',
        'origin_country' => 'India',
        'status' => 'inactive',
    ])->assertOk()
        ->assertJson(['message' => 'Raza actualizada correctamente.']);

    $breed->refresh();

    expect($breed->name)->toBe('Gyr Lechero')
        ->and($breed->code)->toBe('GYR')
        ->and($breed->status)->toBe('inactive');
});

it('devuelve detalle completo y elimina la raza', function () {
    $breed = Breed::create([
        'name' => 'Nelore',
        'code' => 'NEL',
        'origin_country' => 'India',
        'description' => 'Raza de carne.',
        'characteristics' => 'Adaptabilidad tropical.',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.breeds.show', $breed))
        ->assertOk()
        ->assertJsonPath('breed.name', 'Nelore')
        ->assertJsonPath('breed.code', 'NEL')
        ->assertJsonPath('breed.status_label', 'Activo');

    $this->delete(route('admin.breeds.destroy', $breed))
        ->assertOk()
        ->assertJson(['message' => 'Raza eliminada correctamente.']);

    $this->assertDatabaseMissing('breeds', ['id' => $breed->id]);
});

it('valida campos obligatorios, código único y formato del código', function () {
    Breed::create([
        'name' => 'Cebú',
        'code' => 'CEBU',
        'status' => 'active',
    ]);

    $this->postJson(route('admin.breeds.store'), [
        'name' => '',
        'code' => 'CEBU',
        'status' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'code', 'status'])
        ->assertJsonPath('errors.code.0', 'El código de la raza ya está registrado.');

    $this->postJson(route('admin.breeds.store'), [
        'name' => 'Código inválido',
        'code' => 'BR H',
        'status' => 'active',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['code'])
        ->assertJsonPath('errors.code.0', 'El código no debe contener espacios ni caracteres especiales.');
});
