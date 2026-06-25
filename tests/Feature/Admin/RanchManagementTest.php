<?php

use App\Models\Ranch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();

    collect([
        'admin.ranches.index',
        'admin.ranches.store',
        'admin.ranches.update',
        'admin.ranches.destroy',
    ])->each(function (string $permission) {
        Permission::create(['name' => $permission]);
    });

    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);
});

it('muestra el módulo de criaderos', function () {
    $this->get(route('admin.ranches.index'))
        ->assertOk()
        ->assertSee('Criaderos / Haciendas')
        ->assertSee('css/admin-modern.css');
});

it('devuelve el listado para DataTable', function () {
    Ranch::create([
        'name' => 'Hacienda Listada',
        'document_type' => 'RUC',
        'document_number' => '20123456789',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.ranches.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.name', 'Hacienda Listada')
        ->assertSee('badge-success', false);
});

it('crea un criadero con sus archivos', function () {
    Storage::fake('public');

    $response = $this->post(route('admin.ranches.store'), [
        'name' => 'Hacienda El Cebú',
        'business_name' => 'Ganadería El Cebú SAC',
        'document_type' => 'RUC',
        'document_number' => '20123456789',
        'email' => 'contacto@elcebu.test',
        'status' => 'active',
        'logo' => UploadedFile::fake()->image('logo.png'),
        'seal' => UploadedFile::fake()->image('sello.png'),
        'signature' => UploadedFile::fake()->image('firma.png'),
    ]);

    $response->assertOk()
        ->assertJson(['message' => 'Criadero registrado correctamente.']);

    $ranch = Ranch::firstOrFail();

    expect($ranch->name)->toBe('Hacienda El Cebú');
    Storage::disk('public')->assertExists($ranch->logo_path);
    Storage::disk('public')->assertExists($ranch->seal_path);
    Storage::disk('public')->assertExists($ranch->signature_path);
});

it('actualiza datos sin perder archivos existentes', function () {
    Storage::fake('public');

    $logoPath = UploadedFile::fake()->image('logo.png')->store('ranches/logos', 'public');
    $ranch = Ranch::create([
        'name' => 'Criadero Inicial',
        'status' => 'active',
        'logo_path' => $logoPath,
    ]);

    $this->put(route('admin.ranches.update', $ranch), [
        'name' => 'Criadero Actualizado',
        'status' => 'inactive',
    ])->assertOk()
        ->assertJson(['message' => 'Criadero actualizado correctamente.']);

    $ranch->refresh();

    expect($ranch->name)->toBe('Criadero Actualizado')
        ->and($ranch->status)->toBe('inactive')
        ->and($ranch->logo_path)->toBe($logoPath);

    Storage::disk('public')->assertExists($logoPath);
});

it('reemplaza el archivo anterior al subir uno nuevo', function () {
    Storage::fake('public');

    $oldLogoPath = UploadedFile::fake()->image('anterior.png')->store('ranches/logos', 'public');
    $ranch = Ranch::create([
        'name' => 'Criadero',
        'status' => 'active',
        'logo_path' => $oldLogoPath,
    ]);

    $this->put(route('admin.ranches.update', $ranch), [
        'name' => 'Criadero',
        'status' => 'active',
        'logo' => UploadedFile::fake()->image('nuevo.png'),
    ])->assertOk();

    $ranch->refresh();

    Storage::disk('public')->assertMissing($oldLogoPath);
    Storage::disk('public')->assertExists($ranch->logo_path);
});

it('devuelve el detalle completo y elimina mediante soft delete', function () {
    $ranch = Ranch::create([
        'name' => 'Fundo San Martín',
        'department' => 'Lima',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.ranches.show', $ranch))
        ->assertOk()
        ->assertJsonPath('ranch.name', 'Fundo San Martín')
        ->assertJsonPath('ranch.status_label', 'Activo');

    $this->delete(route('admin.ranches.destroy', $ranch))
        ->assertOk()
        ->assertJson(['message' => 'Criadero eliminado correctamente.']);

    $this->assertSoftDeleted('ranches', ['id' => $ranch->id]);
});

it('valida los campos obligatorios y el formato del correo', function () {
    $this->postJson(route('admin.ranches.store'), [
        'name' => '',
        'email' => 'correo-invalido',
        'status' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'status']);
});
