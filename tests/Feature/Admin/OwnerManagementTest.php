<?php

use App\Models\Owner;
use App\Models\User;
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

it('normaliza entidades html en nombres de propietarios', function () {
    $owner = Owner::create([
        'owner_type' => 'company',
        'document_type' => 'RUC',
        'document_number' => '20123456789',
        'full_name' => 'Contacto &amp;amp; Comercial',
        'business_name' => 'E &amp;amp; L ENGINEERS S.A.C.',
        'address' => 'Jr. Angamos Nro 156 &amp;amp; Oficina 2',
        'status' => 'active',
    ]);

    $this->getJson(route('admin.owners.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('data.0.display_name', 'E & L ENGINEERS S.A.C.');

    $this->getJson(route('admin.owners.show', $owner))
        ->assertOk()
        ->assertJsonPath('owner.full_name', 'Contacto & Comercial')
        ->assertJsonPath('owner.business_name', 'E & L ENGINEERS S.A.C.')
        ->assertJsonPath('owner.address', 'Jr. Angamos Nro 156 & Oficina 2');
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

it('sube, conserva y reemplaza la foto del propietario', function () {
    Storage::fake('public');

    $this->post(route('admin.owners.store'), [
        'owner_type' => 'person',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'full_name' => 'Carlos Mendoza',
        'photo' => UploadedFile::fake()->image('propietario.jpg'),
        'status' => 'active',
    ])->assertOk();

    $owner = Owner::firstOrFail();
    $firstPhoto = $owner->photo_path;

    expect($firstPhoto)->not->toBeNull();
    Storage::disk('public')->assertExists($firstPhoto);

    $this->post(route('admin.owners.update', $owner), [
        '_method' => 'PUT',
        'owner_type' => 'person',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'full_name' => 'Carlos Mendoza Actualizado',
        'status' => 'active',
    ])->assertOk();

    $owner->refresh();

    expect($owner->photo_path)->toBe($firstPhoto);
    Storage::disk('public')->assertExists($firstPhoto);

    $this->post(route('admin.owners.update', $owner), [
        '_method' => 'PUT',
        'owner_type' => 'person',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'full_name' => 'Carlos Mendoza Actualizado',
        'photo' => UploadedFile::fake()->image('propietario-nuevo.png'),
        'status' => 'active',
    ])->assertOk();

    $owner->refresh();

    expect($owner->photo_path)->not->toBe($firstPhoto);
    Storage::disk('public')->assertMissing($firstPhoto);
    Storage::disk('public')->assertExists($owner->photo_path);

    $this->getJson(route('admin.owners.show', $owner))
        ->assertOk()
        ->assertJsonPath('owner.photo_path', $owner->photo_path)
        ->assertJsonPath('owner.photo_url', Storage::url($owner->photo_path));
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

it('valida la longitud de DNI y RUC al guardar propietarios', function (
    string $documentType,
    string $documentNumber,
    string $expectedMessage
) {
    $this->postJson(route('admin.owners.store'), [
        'owner_type' => $documentType === 'RUC' ? 'company' : 'person',
        'document_type' => $documentType,
        'document_number' => $documentNumber,
        'full_name' => 'Propietario de prueba',
        'status' => 'active',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['document_number'])
        ->assertJsonPath('errors.document_number.0', $expectedMessage);
})->with([
    ['DNI', '1234567', 'El DNI debe tener 8 dígitos.'],
    ['RUC', '2012345678', 'El RUC debe tener 11 dígitos.'],
]);

it('valida la relación entre tipo de propietario y documento', function (
    string $ownerType,
    string $documentType,
    string $documentNumber,
    string $field,
    string $expectedMessage
) {
    $this->postJson(route('admin.owners.store'), [
        'owner_type' => $ownerType,
        'document_type' => $documentType,
        'document_number' => $documentNumber,
        'full_name' => 'Propietario de prueba',
        'status' => 'active',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors([$field])
        ->assertJsonPath("errors.{$field}.0", $expectedMessage);
})->with([
    ['company', 'DNI', '12345678', 'document_type', 'El tipo de documento seleccionado no es válido.'],
    ['company', 'RUC', '10123456789', 'document_number', 'El RUC de una empresa debe empezar con 20.'],
    ['person', 'RUC', '20123456789', 'document_number', 'El RUC de una persona natural debe empezar con 10.'],
]);
