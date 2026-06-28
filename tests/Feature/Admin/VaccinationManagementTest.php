<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\User;
use App\Models\Vaccination;
use App\Models\Veterinarian;
use Database\Seeders\VaccinationPermissionSeeder;
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
        'admin.vaccinations.index',
        'admin.vaccinations.store',
        'admin.vaccinations.update',
        'admin.vaccinations.destroy',
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

it('muestra el modulo de vacunas del ganado', function () {
    $this->get(route('admin.vaccinations.index'))
        ->assertOk()
        ->assertSee('Vacunas del Ganado')
        ->assertSee('Nueva Vacuna')
        ->assertSee('tableVaccination');
});

it('permite acceder con el rol administrador sembrado', function () {
    $this->seed(VaccinationPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.vaccinations.index'))
        ->assertOk()
        ->assertSee('Nueva Vacuna');
});

it('lista vacunas en DataTable con estados de proxima dosis', function () {
    Vaccination::create([
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'vaccine_name' => 'Aftosa',
        'dose' => '5 ml',
        'batch_number' => 'L-001',
        'application_date' => '2024-05-10',
        'next_due_date' => now()->addMonth()->toDateString(),
    ]);

    $this->getJson(route('admin.vaccinations.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.cattle_name', 'Toro Norte')
        ->assertJsonPath('data.0.cattle_code', 'CEBU-000001')
        ->assertSee('Programada', false);
});

it('muestra estados sin proxima dosis vencida y aplicar hoy', function () {
    Vaccination::create([
        'cattle_id' => $this->cattle->id,
        'vaccine_name' => 'Sin refuerzo',
        'application_date' => '2024-05-10',
    ]);

    Vaccination::create([
        'cattle_id' => $this->cattle->id,
        'vaccine_name' => 'Vencida',
        'application_date' => '2024-05-10',
        'next_due_date' => now()->subDay()->toDateString(),
    ]);

    Vaccination::create([
        'cattle_id' => $this->cattle->id,
        'vaccine_name' => 'Hoy',
        'application_date' => now()->toDateString(),
        'next_due_date' => now()->toDateString(),
    ]);

    $this->getJson(route('admin.vaccinations.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertSee('Sin proxima dosis', false)
        ->assertSee('Vencida', false)
        ->assertSee('Aplicar hoy', false);
});

it('guarda y actualiza vacuna con veterinario opcional', function () {
    $this->post(route('admin.vaccinations.store'), [
        'cattle_id' => $this->cattle->id,
        'vaccine_name' => 'Carbunco',
        'dose' => '2 ml',
        'batch_number' => 'CAR-22',
        'application_date' => '2024-05-10',
        'next_due_date' => '2024-08-10',
        'observations' => 'Aplicada sin reaccion.',
    ])->assertOk()
        ->assertJson(['message' => 'Vacuna registrada correctamente.']);

    $vaccination = Vaccination::firstOrFail();

    expect($vaccination->veterinarian_id)->toBeNull()
        ->and($vaccination->vaccine_name)->toBe('Carbunco');

    $this->put(route('admin.vaccinations.update', $vaccination), [
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'vaccine_name' => 'Carbunco Plus',
        'dose' => '3 ml',
        'batch_number' => 'CAR-23',
        'application_date' => '2024-05-11',
        'next_due_date' => '2024-08-11',
        'observations' => 'Actualizada.',
    ])->assertOk()
        ->assertJson(['message' => 'Vacuna actualizada correctamente.']);

    $vaccination->refresh();

    expect($vaccination->veterinarian_id)->toBe($this->veterinarian->id)
        ->and($vaccination->vaccine_name)->toBe('Carbunco Plus');
});

it('muestra detalle completo y elimina', function () {
    $vaccination = Vaccination::create([
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'vaccine_name' => 'Brucelosis',
        'dose' => '1 ml',
        'batch_number' => 'BRU-01',
        'application_date' => '2024-05-10',
        'next_due_date' => '2024-06-10',
        'observations' => 'Control reproductivo.',
    ]);

    $this->getJson(route('admin.vaccinations.show', $vaccination))
        ->assertOk()
        ->assertJsonPath('vaccination.cattle_code', 'CEBU-000001')
        ->assertJsonPath('vaccination.cattle_owner_name', 'Carlos Mendoza')
        ->assertJsonPath('vaccination.veterinarian_name', 'Dra. Ana Torres')
        ->assertJsonPath('vaccination.next_due_status_label', 'Vencida');

    $this->delete(route('admin.vaccinations.destroy', $vaccination))
        ->assertOk()
        ->assertJson(['message' => 'Vacuna eliminada correctamente.']);

    $this->assertDatabaseMissing('vaccinations', ['id' => $vaccination->id]);
});

it('valida obligatorios y fecha de proxima dosis', function () {
    $this->postJson(route('admin.vaccinations.store'), [
        'cattle_id' => '',
        'vaccine_name' => '',
        'application_date' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['cattle_id', 'vaccine_name', 'application_date'])
        ->assertJsonPath('errors.cattle_id.0', 'Seleccione el ganado.')
        ->assertJsonPath('errors.vaccine_name.0', 'Ingrese el nombre de la vacuna.')
        ->assertJsonPath('errors.application_date.0', 'Ingrese la fecha de aplicacion.');

    $this->postJson(route('admin.vaccinations.store'), [
        'cattle_id' => $this->cattle->id,
        'vaccine_name' => 'Aftosa',
        'application_date' => '2024-05-10',
        'next_due_date' => '2024-05-01',
    ])->assertUnprocessable()
        ->assertJsonPath('errors.next_due_date.0', 'La proxima dosis no puede ser anterior a la fecha aplicada.');
});

it('incluye vacunas recientes en el detalle del ganado', function () {
    Vaccination::create([
        'cattle_id' => $this->cattle->id,
        'veterinarian_id' => $this->veterinarian->id,
        'vaccine_name' => 'Aftosa',
        'dose' => '5 ml',
        'batch_number' => 'AFT-01',
        'application_date' => '2024-05-10',
        'next_due_date' => now()->addMonth()->toDateString(),
    ]);

    $this->getJson(route('admin.cattle.show', $this->cattle))
        ->assertOk()
        ->assertJsonPath('cattle.vaccinations.0.vaccine_name', 'Aftosa')
        ->assertJsonPath('cattle.vaccinations.0.next_due_status_label', 'Programada')
        ->assertJsonPath('cattle.vaccinations.0.veterinarian_name', 'Dra. Ana Torres');
});
