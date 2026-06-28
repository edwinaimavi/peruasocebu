<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\User;
use App\Models\WeightRecord;
use Database\Seeders\WeightRecordPermissionSeeder;
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
        'admin.weight-records.index',
        'admin.weight-records.store',
        'admin.weight-records.update',
        'admin.weight-records.destroy',
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
});

it('muestra el modulo de historial de pesajes', function () {
    $this->get(route('admin.weight-records.index'))
        ->assertOk()
        ->assertSee('Historial de Pesajes')
        ->assertSee('Nuevo Pesaje')
        ->assertSee('tableWeightRecord');
});

it('permite acceder con el rol administrador sembrado', function () {
    $this->seed(WeightRecordPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.weight-records.index'))
        ->assertOk()
        ->assertSee('Nuevo Pesaje');
});

it('lista pesajes en DataTable con badges', function () {
    WeightRecord::create([
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 450.25,
        'record_date' => '2024-05-10',
        'body_condition' => 'Buena',
    ]);

    $this->getJson(route('admin.weight-records.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.cattle_name', 'Toro Norte')
        ->assertJsonPath('data.0.cattle_code', 'CEBU-000001')
        ->assertSee('450.25 kg', false)
        ->assertSee('Buena', false);
});

it('guarda pesaje y actualiza peso actual solo con el mas reciente', function () {
    $this->post(route('admin.weight-records.store'), [
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 420,
        'record_date' => '2024-05-01',
        'body_condition' => 'Regular',
    ])->assertOk()
        ->assertJson(['message' => 'Pesaje registrado correctamente.']);

    $this->cattle->refresh();
    expect((float) $this->cattle->weight_kg)->toBe(420.0);

    $this->post(route('admin.weight-records.store'), [
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 410,
        'record_date' => '2024-04-01',
    ])->assertOk();

    $this->cattle->refresh();
    expect((float) $this->cattle->weight_kg)->toBe(420.0);

    $this->post(route('admin.weight-records.store'), [
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 450,
        'record_date' => '2024-06-01',
    ])->assertOk();

    $this->cattle->refresh();
    expect((float) $this->cattle->weight_kg)->toBe(450.0);
});

it('edita y elimina recalculando el peso actual', function () {
    $old = WeightRecord::create([
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 390,
        'record_date' => '2024-04-01',
    ]);

    $latest = WeightRecord::create([
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 430,
        'record_date' => '2024-05-01',
    ]);

    $this->put(route('admin.weight-records.update', $old), [
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 460,
        'record_date' => '2024-06-01',
        'body_condition' => 'Excelente',
    ])->assertOk()
        ->assertJson(['message' => 'Pesaje actualizado correctamente.']);

    $this->cattle->refresh();
    expect((float) $this->cattle->weight_kg)->toBe(460.0);

    $this->delete(route('admin.weight-records.destroy', $old->refresh()))
        ->assertOk()
        ->assertJson(['message' => 'Pesaje eliminado correctamente.']);

    $this->cattle->refresh();
    expect((float) $this->cattle->weight_kg)->toBe(430.0);

    $this->delete(route('admin.weight-records.destroy', $latest))
        ->assertOk();

    $this->cattle->refresh();
    expect($this->cattle->weight_kg)->toBeNull();
});

it('muestra detalle completo y evita duplicados exactos', function () {
    $record = WeightRecord::create([
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 450,
        'record_date' => '2024-05-10',
        'body_condition' => 'Buena',
        'observations' => 'Ganancia estable.',
    ]);

    $this->getJson(route('admin.weight-records.show', $record))
        ->assertOk()
        ->assertJsonPath('record.cattle_code', 'CEBU-000001')
        ->assertJsonPath('record.cattle_owner_name', 'Carlos Mendoza')
        ->assertJsonPath('record.weight_kg_formatted', '450.00 kg')
        ->assertJsonPath('record.record_date', '2024-05-10');

    $this->postJson(route('admin.weight-records.store'), [
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 450,
        'record_date' => '2024-05-10',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['weight_kg'])
        ->assertJsonPath('errors.weight_kg.0', 'Ya existe un pesaje registrado para este ganado con la misma fecha y peso.');
});

it('valida campos obligatorios de pesaje', function () {
    $this->postJson(route('admin.weight-records.store'), [
        'cattle_id' => '',
        'weight_kg' => 0,
        'record_date' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['cattle_id', 'weight_kg', 'record_date'])
        ->assertJsonPath('errors.cattle_id.0', 'Seleccione el ganado.')
        ->assertJsonPath('errors.weight_kg.0', 'El peso debe ser mayor a cero.')
        ->assertJsonPath('errors.record_date.0', 'Ingrese la fecha del pesaje.');
});

it('incluye pesajes recientes en el detalle del ganado', function () {
    WeightRecord::create([
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 430,
        'record_date' => '2024-05-01',
        'body_condition' => 'Regular',
    ]);

    WeightRecord::create([
        'cattle_id' => $this->cattle->id,
        'weight_kg' => 450,
        'record_date' => '2024-06-01',
        'body_condition' => 'Buena',
    ]);

    $this->getJson(route('admin.cattle.show', $this->cattle))
        ->assertOk()
        ->assertJsonPath('cattle.weight_records.0.weight_kg', '450.00 kg')
        ->assertJsonPath('cattle.weight_records.0.body_condition', 'Buena')
        ->assertJsonPath('cattle.latest_weight_record.weight_kg', '450.00 kg')
        ->assertJsonPath('cattle.previous_weight_record.difference', '20.00');
});
