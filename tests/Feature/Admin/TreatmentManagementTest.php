<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\Treatment;
use App\Models\User;
use App\Models\Veterinarian;
use Database\Seeders\TreatmentPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        'admin.cattle.index',
        'admin.treatments.index',
        'admin.treatments.store',
        'admin.treatments.update',
        'admin.treatments.destroy',
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

it('muestra el modulo de tratamientos medicos', function () {
    $this->get(route('admin.treatments.index'))
        ->assertOk()
        ->assertSee('Tratamientos Medicos')
        ->assertSee('Nuevo Tratamiento')
        ->assertSee('tableTreatment');
});

it('permite acceder con el rol administrador sembrado', function () {
    $this->seed(TreatmentPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.treatments.index'))
        ->assertOk()
        ->assertSee('Nuevo Tratamiento');
});

it('lista tratamientos en DataTable con badges', function () {
    Treatment::create([
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'treatment_date' => '2024-05-10',
        'treatment_name' => 'Antibiotico preventivo',
        'medicine' => 'Oxitetraciclina',
        'dose' => '5 ml',
        'duration' => '3 dias',
    ]);

    $this->getJson(route('admin.treatments.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.cattle_name', 'Toro Norte')
        ->assertJsonPath('data.0.cattle_code', 'CEBU-000001')
        ->assertSee('Antibiotico preventivo', false)
        ->assertSee('Con veterinario', false);
});

it('guarda y actualiza tratamiento con veterinario opcional', function () {
    $this->post(route('admin.treatments.store'), [
        'cattle_id' => $this->cattle->id,
        'treatment_date' => '2024-05-10',
        'treatment_name' => 'Antiinflamatorio',
        'medicine' => 'Flunixin',
        'dose' => '2 ml',
        'duration' => 'Aplicacion unica',
        'reason' => 'Dolor muscular.',
        'observations' => 'Sin reaccion.',
    ])->assertOk()
        ->assertJson(['message' => 'Tratamiento registrado correctamente.']);

    $treatment = Treatment::firstOrFail();

    expect($treatment->veterinarian_id)->toBeNull()
        ->and($treatment->treatment_name)->toBe('Antiinflamatorio');

    $this->put(route('admin.treatments.update', $treatment), [
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'treatment_date' => '2024-05-11',
        'treatment_name' => 'Antiinflamatorio controlado',
        'medicine' => 'Flunixin',
        'dose' => '3 ml',
        'duration' => '2 dias',
    ])->assertOk()
        ->assertJson(['message' => 'Tratamiento actualizado correctamente.']);

    $treatment->refresh();

    expect($treatment->veterinarian_id)->toBe($this->veterinarian->id)
        ->and($treatment->treatment_name)->toBe('Antiinflamatorio controlado');
});

it('muestra detalle completo y elimina', function () {
    $treatment = Treatment::create([
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'treatment_date' => '2024-05-10',
        'treatment_name' => 'Antibiotico preventivo',
        'medicine' => 'Oxitetraciclina',
        'dose' => '5 ml',
        'duration' => '3 dias',
        'reason' => 'Herida superficial',
        'observations' => 'Evolucion favorable.',
    ]);

    $this->getJson(route('admin.treatments.show', $treatment))
        ->assertOk()
        ->assertJsonPath('treatment.cattle_code', 'CEBU-000001')
        ->assertJsonPath('treatment.cattle_owner_name', 'Carlos Mendoza')
        ->assertJsonPath('treatment.veterinarian_name', 'Dra. Ana Torres')
        ->assertJsonPath('treatment.veterinarian_specialty', 'Sanidad bovina')
        ->assertJsonPath('treatment.treatment_date', '2024-05-10');

    $this->delete(route('admin.treatments.destroy', $treatment))
        ->assertOk()
        ->assertJson(['message' => 'Tratamiento eliminado correctamente.']);

    $this->assertDatabaseMissing('treatments', ['id' => $treatment->id]);
});

it('valida obligatorios y veterinario existente', function () {
    $this->postJson(route('admin.treatments.store'), [
        'cattle_id' => '',
        'treatment_date' => '',
        'treatment_name' => '',
        'veterinarian_id' => 999,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['cattle_id', 'treatment_date', 'treatment_name', 'veterinarian_id'])
        ->assertJsonPath('errors.cattle_id.0', 'Seleccione el ganado.')
        ->assertJsonPath('errors.treatment_date.0', 'Ingrese la fecha del tratamiento.')
        ->assertJsonPath('errors.treatment_name.0', 'Ingrese el nombre del tratamiento.')
        ->assertJsonPath('errors.veterinarian_id.0', 'El veterinario seleccionado no es valido.');
});

it('incluye tratamientos recientes en el detalle del ganado', function () {
    Treatment::create([
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'treatment_date' => '2024-05-10',
        'treatment_name' => 'Antibiotico preventivo',
        'medicine' => 'Oxitetraciclina',
        'dose' => '5 ml',
        'duration' => '3 dias',
    ]);

    $this->getJson(route('admin.cattle.show', $this->cattle))
        ->assertOk()
        ->assertJsonPath('cattle.treatments.0.treatment_name', 'Antibiotico preventivo')
        ->assertJsonPath('cattle.treatments.0.medicine', 'Oxitetraciclina')
        ->assertJsonPath('cattle.treatments.0.veterinarian_name', 'Dra. Ana Torres');
});
