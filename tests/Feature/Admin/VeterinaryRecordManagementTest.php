<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\User;
use App\Models\Veterinarian;
use App\Models\VeterinaryRecord;
use Database\Seeders\VeterinaryRecordPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        'admin.veterinary-records.index',
        'admin.veterinary-records.store',
        'admin.veterinary-records.update',
        'admin.veterinary-records.destroy',
    ])->each(fn (string $permission) => Permission::create(['name' => $permission]));

    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);

    $this->breed = Breed::create([
        'name' => 'Cebu',
        'code' => 'CEBU',
        'status' => 'active',
    ]);

    $this->ranch = Ranch::create([
        'name' => 'Hacienda Norte',
        'status' => 'active',
    ]);

    $this->owner = Owner::create([
        'owner_type' => 'person',
        'full_name' => 'Carlos Mendoza',
        'status' => 'active',
    ]);

    $this->cattle = Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Toro Norte',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->owner->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'available',
    ]);

    $this->veterinarian = Veterinarian::create([
        'full_name' => 'Dra. Ana Torres',
        'license_number' => 'CMVP-12345',
        'specialty' => 'Sanidad bovina',
        'status' => 'active',
    ]);
});

it('muestra el modulo de revisiones veterinarias', function () {
    $this->get(route('admin.veterinary-records.index'))
        ->assertOk()
        ->assertSee('Revisiones Veterinarias')
        ->assertSee('Nueva Revision')
        ->assertSee('tableVeterinaryRecord');
});

it('permite acceder con el rol administrador sembrado', function () {
    $this->seed(VeterinaryRecordPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.veterinary-records.index'))
        ->assertOk()
        ->assertSee('Nueva Revision');
});

it('lista revisiones en DataTable con badges', function () {
    VeterinaryRecord::create([
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'record_date' => '2024-05-10',
        'record_type' => 'control',
        'diagnosis' => 'Control general',
    ]);

    $this->getJson(route('admin.veterinary-records.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.cattle_name', 'Toro Norte')
        ->assertSee('Control', false)
        ->assertSee('Sin archivo', false);
});

it('guarda revision sin veterinario ni archivo', function () {
    $this->post(route('admin.veterinary-records.store'), [
        'cattle_id' => $this->cattle->id,
        'record_date' => '2024-05-10',
        'record_type' => 'checkup',
        'diagnosis' => 'Revision general',
    ])->assertOk()
        ->assertJson(['message' => 'Revision veterinaria registrada correctamente.']);

    $record = VeterinaryRecord::firstOrFail();

    expect($record->veterinarian_id)->toBeNull()
        ->and($record->record_type)->toBe('checkup');
});

it('guarda edita conserva y reemplaza archivo adjunto', function () {
    Storage::fake('public');

    $this->post(route('admin.veterinary-records.store'), [
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'record_date' => '2024-05-10',
        'record_type' => 'illness',
        'diagnosis' => 'Fiebre leve',
        'treatment' => 'Antiinflamatorio',
        'next_visit_date' => '2024-05-20',
        'document_file' => UploadedFile::fake()->create('informe.pdf', 200, 'application/pdf'),
    ])->assertOk();

    $record = VeterinaryRecord::firstOrFail();
    $firstDocument = $record->document_path;

    expect($firstDocument)->not->toBeNull();
    Storage::disk('public')->assertExists($firstDocument);

    $this->put(route('admin.veterinary-records.update', $record), [
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'record_date' => '2024-05-11',
        'record_type' => 'control',
        'diagnosis' => 'Control posterior',
    ])->assertOk();

    $record->refresh();

    expect($record->document_path)->toBe($firstDocument);
    Storage::disk('public')->assertExists($firstDocument);

    $this->post(route('admin.veterinary-records.update', $record), [
        '_method' => 'PUT',
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'record_date' => '2024-05-12',
        'record_type' => 'certification',
        'document_file' => UploadedFile::fake()->image('certificado.png'),
    ])->assertOk();

    $record->refresh();

    expect($record->document_path)->not->toBe($firstDocument);
    Storage::disk('public')->assertMissing($firstDocument);
    Storage::disk('public')->assertExists($record->document_path);
});

it('muestra detalle completo y elimina', function () {
    $record = VeterinaryRecord::create([
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'record_date' => '2024-05-10',
        'record_type' => 'emergency',
        'diagnosis' => 'Herida superficial',
        'treatment' => 'Limpieza y antibiotico',
    ]);

    $this->getJson(route('admin.veterinary-records.show', $record))
        ->assertOk()
        ->assertJsonPath('record.cattle_code', 'CEBU-000001')
        ->assertJsonPath('record.veterinarian_name', 'Dra. Ana Torres')
        ->assertJsonPath('record.record_type_label', 'Emergencia')
        ->assertJsonPath('record.cattle_owner_name', 'Carlos Mendoza');

    $this->delete(route('admin.veterinary-records.destroy', $record))
        ->assertOk()
        ->assertJson(['message' => 'Revision veterinaria eliminada correctamente.']);

    $this->assertDatabaseMissing('veterinary_records', ['id' => $record->id]);
});

it('valida obligatorios fecha proxima y archivo', function () {
    $this->postJson(route('admin.veterinary-records.store'), [
        'cattle_id' => '',
        'record_date' => '',
        'record_type' => '',
        'next_visit_date' => '2024-05-01',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['cattle_id', 'record_date', 'record_type']);

    $this->postJson(route('admin.veterinary-records.store'), [
        'cattle_id' => $this->cattle->id,
        'record_date' => '2024-05-10',
        'record_type' => 'checkup',
        'next_visit_date' => '2024-05-01',
    ])->assertUnprocessable()
        ->assertJsonPath('errors.next_visit_date.0', 'La proxima visita no puede ser anterior a la fecha de atencion.');
});
