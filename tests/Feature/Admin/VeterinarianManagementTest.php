<?php

use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        'admin.veterinarians.index',
        'admin.veterinarians.store',
        'admin.veterinarians.update',
        'admin.veterinarians.destroy',
    ])->each(function (string $permission) {
        Permission::create(['name' => $permission]);
    });

    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);
});

it('muestra el módulo de veterinarios', function () {
    $this->get(route('admin.veterinarians.index'))
        ->assertOk()
        ->assertSee('Veterinarios / Certificadores')
        ->assertSee('Nuevo Veterinario')
        ->assertSee('tableVeterinarian');
});

it('devuelve el listado para DataTable', function () {
    Veterinarian::create([
        'full_name' => 'Dra. Ana Torres',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'license_number' => 'CMVP 12345',
        'specialty' => 'Reproducción bovina',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.veterinarians.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.full_name', 'Dra. Ana Torres')
        ->assertSee('badge-success', false)
        ->assertSee('Activo', false);
});

it('crea, conserva y reemplaza la firma digital del veterinario', function () {
    Storage::fake('public');

    $this->post(route('admin.veterinarians.store'), [
        'full_name' => 'Dr. Carlos Mendoza',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'license_number' => 'CMVP 9999',
        'specialty' => 'Sanidad animal',
        'signature' => UploadedFile::fake()->image('firma.jpg', 320, 120),
        'status' => 'active',
    ])->assertOk()
        ->assertJson(['message' => 'Veterinario registrado correctamente.']);

    $veterinarian = Veterinarian::firstOrFail();
    $firstSignature = $veterinarian->signature_path;

    expect($firstSignature)->not->toBeNull();
    Storage::disk('public')->assertExists($firstSignature);

    $this->post(route('admin.veterinarians.update', $veterinarian), [
        '_method' => 'PUT',
        'full_name' => 'Dr. Carlos Mendoza Actualizado',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'status' => 'inactive',
    ])->assertOk()
        ->assertJson(['message' => 'Veterinario actualizado correctamente.']);

    $veterinarian->refresh();

    expect($veterinarian->signature_path)->toBe($firstSignature)
        ->and($veterinarian->status)->toBe('inactive');
    Storage::disk('public')->assertExists($firstSignature);

    $this->post(route('admin.veterinarians.update', $veterinarian), [
        '_method' => 'PUT',
        'full_name' => 'Dr. Carlos Mendoza Actualizado',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'signature' => UploadedFile::fake()->image('firma-nueva.png', 320, 120),
        'status' => 'active',
    ])->assertOk();

    $veterinarian->refresh();

    expect($veterinarian->signature_path)->not->toBe($firstSignature);
    Storage::disk('public')->assertMissing($firstSignature);
    Storage::disk('public')->assertExists($veterinarian->signature_path);

    $this->getJson(route('admin.veterinarians.show', $veterinarian))
        ->assertOk()
        ->assertJsonPath('veterinarian.signature_path', $veterinarian->signature_path)
        ->assertJsonPath('veterinarian.signature_url', Storage::url($veterinarian->signature_path));
});

it('devuelve el detalle completo y elimina mediante soft delete', function () {
    $veterinarian = Veterinarian::create([
        'full_name' => 'Dra. María López',
        'document_type' => 'RUC',
        'document_number' => '10123456789',
        'license_number' => 'CMVP 7777',
        'specialty' => 'Certificación',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.veterinarians.show', $veterinarian))
        ->assertOk()
        ->assertJsonPath('veterinarian.full_name', 'Dra. María López')
        ->assertJsonPath('veterinarian.document_type_label', 'RUC')
        ->assertJsonPath('veterinarian.status_label', 'Activo');

    $this->delete(route('admin.veterinarians.destroy', $veterinarian))
        ->assertOk()
        ->assertJson(['message' => 'Veterinario eliminado correctamente.']);

    $this->assertSoftDeleted('veterinarians', ['id' => $veterinarian->id]);
});

it('valida campos obligatorios, correo y firma', function () {
    $this->postJson(route('admin.veterinarians.store'), [
        'full_name' => '',
        'document_type' => 'INVALID',
        'email' => 'correo-invalido',
        'signature' => UploadedFile::fake()->create('firma.pdf', 10, 'application/pdf'),
        'status' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors([
            'full_name',
            'document_type',
            'email',
            'signature',
            'status',
        ]);
});

it('valida DNI y RUC de persona natural al guardar', function (
    string $documentType,
    string $documentNumber,
    string $expectedMessage
) {
    $this->postJson(route('admin.veterinarians.store'), [
        'full_name' => 'Veterinario de prueba',
        'document_type' => $documentType,
        'document_number' => $documentNumber,
        'status' => 'active',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['document_number'])
        ->assertJsonPath('errors.document_number.0', $expectedMessage);
})->with([
    ['DNI', '1234567', 'El DNI debe tener 8 dígitos.'],
    ['RUC', '1012345678', 'El RUC debe tener 11 dígitos.'],
    ['RUC', '20123456789', 'El RUC del veterinario debe ser de persona natural y empezar con 10.'],
]);
