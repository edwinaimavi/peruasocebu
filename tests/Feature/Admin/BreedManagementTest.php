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

it('muestra el modulo de razas', function () {
    $this->get(route('admin.breeds.index'))
        ->assertOk()
        ->assertSee('Razas de Ganado')
        ->assertSee('Nueva Raza')
        ->assertSee('tableBreed');
});

it('devuelve el listado para DataTable', function () {
    Breed::create([
        'name' => 'Brahman',
        'code' => 'BR001',
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
        ->assertSee('BR001', false);
});

it('genera el codigo automaticamente al crear y mantiene el codigo si el nombre no cambia', function () {
    $this->post(route('admin.breeds.store'), [
        'name' => 'Gyr',
        'code' => '',
        'origin_country' => 'India',
        'description' => 'Raza lechera cebuina.',
        'characteristics' => 'Rusticidad y aptitud lechera.',
        'status' => 'active',
    ])->assertOk()
        ->assertJson(['message' => 'Raza registrada correctamente.']);

    $breed = Breed::firstOrFail();

    expect($breed->code)->toBe('GY001');

    $this->put(route('admin.breeds.update', $breed), [
        'name' => 'Gyr',
        'code' => 'GY001',
        'origin_country' => 'India',
        'status' => 'inactive',
    ])->assertOk()
        ->assertJson(['message' => 'Raza actualizada correctamente.']);

    $breed->refresh();

    expect($breed->name)->toBe('Gyr')
        ->and($breed->code)->toBe('GY001')
        ->and($breed->status)->toBe('inactive');
});

it('evita codigos repetidos y regenera al cambiar el nombre', function () {
    Breed::create([
        'name' => 'Gyr',
        'code' => 'GY001',
        'status' => 'active',
    ]);

    $this->post(route('admin.breeds.store'), [
        'name' => 'Gyr Lechero',
        'code' => 'GY001',
        'status' => 'active',
    ])->assertOk();

    $newBreed = Breed::where('name', 'Gyr Lechero')->firstOrFail();

    expect($newBreed->code)->toBe('GY002');

    $this->put(route('admin.breeds.update', $newBreed), [
        'name' => 'Brahman',
        'code' => 'GY002',
        'status' => 'active',
    ])->assertOk();

    $newBreed->refresh();

    expect($newBreed->code)->toBe('BR001');
});

it('devuelve detalle completo y elimina la raza', function () {
    $breed = Breed::create([
        'name' => 'Nelore',
        'code' => 'NE001',
        'origin_country' => 'India',
        'description' => 'Raza de carne.',
        'characteristics' => 'Adaptabilidad tropical.',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.breeds.show', $breed))
        ->assertOk()
        ->assertJsonPath('breed.name', 'Nelore')
        ->assertJsonPath('breed.code', 'NE001')
        ->assertJsonPath('breed.status_label', 'Activo');

    $this->delete(route('admin.breeds.destroy', $breed))
        ->assertOk()
        ->assertJson(['message' => 'Raza eliminada correctamente.']);

    $this->assertDatabaseMissing('breeds', ['id' => $breed->id]);
});

it('valida campos obligatorios y permite generar el codigo aunque el temporal venga repetido', function () {
    Breed::create([
        'name' => 'Cebu',
        'code' => 'CE001',
        'status' => 'active',
    ]);

    $this->postJson(route('admin.breeds.store'), [
        'name' => '',
        'code' => 'CE001',
        'status' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'status']);

    $this->postJson(route('admin.breeds.store'), [
        'name' => 'Cebu Lechero',
        'code' => 'CE001',
        'status' => 'active',
    ])->assertOk();

    expect(Breed::where('name', 'Cebu Lechero')->firstOrFail()->code)->toBe('CE002');
});
