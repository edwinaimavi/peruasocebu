<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\CattleGenealogyLink;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\ReproductionRecord;
use App\Models\User;
use Database\Seeders\ReproductionRecordPermissionSeeder;
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
        'admin.reproduction-records.index',
        'admin.reproduction-records.store',
        'admin.reproduction-records.update',
        'admin.reproduction-records.destroy',
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

    $this->female = Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Vaca Madre',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->owner->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'available',
    ]);

    $this->male = Cattle::create([
        'code' => 'CEBU-000002',
        'name' => 'Toro Padre',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'available',
    ]);

    $this->offspring = Cattle::create([
        'code' => 'CEBU-000003',
        'name' => 'Cria Nacida',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);
});

it('muestra el modulo de historial reproductivo', function () {
    $this->get(route('admin.reproduction-records.index'))
        ->assertOk()
        ->assertSee('Historial Reproductivo')
        ->assertSee('Nuevo Registro')
        ->assertSee('tableReproductionRecord');
});

it('permite acceder con el rol administrador sembrado', function () {
    $this->seed(ReproductionRecordPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.reproduction-records.index'))
        ->assertOk()
        ->assertSee('Nuevo Registro');
});

it('lista registros reproductivos en DataTable con badges', function () {
    ReproductionRecord::create([
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->male->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_result' => 'positive',
        'birth_date' => '2025-02-10',
        'offspring_cattle_id' => $this->offspring->id,
    ]);

    $this->getJson(route('admin.reproduction-records.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.cattle_name', 'Vaca Madre')
        ->assertJsonPath('data.0.cattle_code', 'CEBU-000001')
        ->assertSee('Monta natural', false)
        ->assertSee('Positivo', false)
        ->assertSee('Parto registrado', false);
});

it('guarda edita muestra detalle y elimina', function () {
    $this->post(route('admin.reproduction-records.store'), [
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->male->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_check_date' => '2024-08-10',
        'pregnancy_result' => 'pending',
        'observations' => 'Primer servicio registrado.',
    ])->assertOk()
        ->assertJson(['message' => 'Registro reproductivo guardado correctamente.']);

    $record = ReproductionRecord::firstOrFail();

    $this->put(route('admin.reproduction-records.update', $record), [
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->male->id,
        'method' => 'artificial_insemination',
        'reproduction_date' => '2024-05-10',
        'pregnancy_check_date' => '2024-08-10',
        'pregnancy_result' => 'positive',
        'birth_date' => '2025-02-10',
        'offspring_cattle_id' => $this->offspring->id,
    ])->assertOk()
        ->assertJson(['message' => 'Registro reproductivo actualizado correctamente.']);

    $record->refresh();

    expect($record->method)->toBe('artificial_insemination')
        ->and($record->offspring_cattle_id)->toBe($this->offspring->id);

    $this->getJson(route('admin.reproduction-records.show', $record))
        ->assertOk()
        ->assertJsonPath('record.cattle_code', 'CEBU-000001')
        ->assertJsonPath('record.partner_code', 'CEBU-000002')
        ->assertJsonPath('record.offspring_label', 'CEBU-000003 - Cria Nacida')
        ->assertJsonPath('record.method_label', 'Inseminacion artificial')
        ->assertJsonPath('record.birth_date', '2025-02-10');

    $this->delete(route('admin.reproduction-records.destroy', $record))
        ->assertOk()
        ->assertJson(['message' => 'Registro reproductivo eliminado correctamente.']);

    $this->assertDatabaseMissing('reproduction_records', ['id' => $record->id]);
});

it('valida obligatorios fechas y relaciones basicas', function () {
    $this->postJson(route('admin.reproduction-records.store'), [
        'cattle_id' => '',
        'partner_cattle_id' => '',
        'method' => '',
        'reproduction_date' => '',
        'pregnancy_result' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['cattle_id', 'method', 'reproduction_date', 'pregnancy_result'])
        ->assertJsonPath('errors.cattle_id.0', 'Seleccione el animal principal.')
        ->assertJsonPath('errors.method.0', 'Seleccione el metodo reproductivo.')
        ->assertJsonPath('errors.reproduction_date.0', 'Ingrese la fecha reproductiva.');

    $this->postJson(route('admin.reproduction-records.store'), [
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->female->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_check_date' => '2024-05-01',
        'pregnancy_result' => 'pending',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['partner_cattle_id', 'pregnancy_check_date']);
});

it('valida sexo y reglas de prenez parto y cria', function () {
    $this->postJson(route('admin.reproduction-records.store'), [
        'cattle_id' => $this->male->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_result' => 'pending',
    ])->assertUnprocessable()
        ->assertJsonPath('errors.cattle_id.0', 'El animal principal del registro reproductivo debe ser una hembra.');

    $this->postJson(route('admin.reproduction-records.store'), [
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->offspring->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_result' => 'pending',
    ])->assertUnprocessable()
        ->assertJsonPath('errors.partner_cattle_id.0', 'La pareja seleccionada debe ser un macho.');

    $this->postJson(route('admin.reproduction-records.store'), [
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->male->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_result' => 'negative',
        'birth_date' => '2025-02-10',
        'offspring_cattle_id' => $this->offspring->id,
    ])->assertUnprocessable()
        ->assertJsonPath('errors.pregnancy_result.0', 'Si el resultado es negativo, no puede registrar parto ni cria.');

    $this->postJson(route('admin.reproduction-records.store'), [
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->male->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_result' => 'positive',
        'offspring_cattle_id' => $this->offspring->id,
    ])->assertUnprocessable()
        ->assertJsonPath('errors.birth_date.0', 'Para registrar una cria nacida debe ingresar la fecha de parto.');
});

it('sincroniza cria con ganado y genealogia sin duplicar', function () {
    $this->post(route('admin.reproduction-records.store'), [
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->male->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_result' => 'positive',
        'birth_date' => '2025-02-10',
        'offspring_cattle_id' => $this->offspring->id,
    ])->assertOk();

    $this->offspring->refresh();

    expect($this->offspring->mother_id)->toBe($this->female->id)
        ->and($this->offspring->father_id)->toBe($this->male->id);

    $this->assertDatabaseHas('cattle_genealogy_links', [
        'cattle_id' => $this->offspring->id,
        'relative_cattle_id' => $this->female->id,
        'relation_type' => 'mother',
        'generation_level' => 1,
    ]);

    $this->assertDatabaseHas('cattle_genealogy_links', [
        'cattle_id' => $this->offspring->id,
        'relative_cattle_id' => $this->male->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ]);

    $record = ReproductionRecord::firstOrFail();

    $this->put(route('admin.reproduction-records.update', $record), [
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->male->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_result' => 'positive',
        'birth_date' => '2025-02-10',
        'offspring_cattle_id' => $this->offspring->id,
    ])->assertOk();

    expect(CattleGenealogyLink::where('cattle_id', $this->offspring->id)->where('relation_type', 'mother')->count())->toBe(1)
        ->and(CattleGenealogyLink::where('cattle_id', $this->offspring->id)->where('relation_type', 'father')->count())->toBe(1);
});

it('incluye historial reproductivo en detalle de ganado', function () {
    ReproductionRecord::create([
        'cattle_id' => $this->female->id,
        'partner_cattle_id' => $this->male->id,
        'method' => 'natural_mating',
        'reproduction_date' => '2024-05-10',
        'pregnancy_result' => 'positive',
        'birth_date' => '2025-02-10',
        'offspring_cattle_id' => $this->offspring->id,
    ]);

    $this->getJson(route('admin.cattle.show', $this->female))
        ->assertOk()
        ->assertJsonPath('cattle.reproduction_records.0.method_label', 'Monta natural')
        ->assertJsonPath('cattle.reproduction_records.0.partner_label', 'CEBU-000002 - Toro Padre')
        ->assertJsonPath('cattle.reproduction_records.0.offspring_label', 'CEBU-000003 - Cria Nacida');

    $this->getJson(route('admin.cattle.show', $this->male))
        ->assertOk()
        ->assertJsonPath('cattle.reproduction_as_partner.0.cattle_label', 'CEBU-000001 - Vaca Madre');
});
