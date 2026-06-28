<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\Owner;
use App\Models\OwnershipHistory;
use App\Models\Ranch;
use App\Models\User;
use Database\Seeders\OwnershipHistoryPermissionSeeder;
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
        'admin.ownership-histories.index',
        'admin.ownership-histories.store',
        'admin.ownership-histories.update',
        'admin.ownership-histories.destroy',
        'admin.cattle.index',
        'admin.cattle.store',
        'admin.cattle.update',
        'admin.cattle.destroy',
    ])->each(function (string $permission) {
        Permission::create(['name' => $permission]);
    });

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

    $this->secondOwner = Owner::create([
        'owner_type' => 'company',
        'full_name' => 'Ana Torres',
        'business_name' => 'Ganaderia Sur SAC',
        'status' => 'active',
    ]);

    $this->cattle = Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Toro Norte',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'available',
    ]);
});

it('muestra el modulo de historial de propietarios', function () {
    $this->get(route('admin.ownership-histories.index'))
        ->assertOk()
        ->assertSee('Historial de Propietarios')
        ->assertSee('Nuevo Historial')
        ->assertSee('tableOwnershipHistory');
});

it('permite acceder con el rol administrador sembrado', function () {
    $this->seed(OwnershipHistoryPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.ownership-histories.index'))
        ->assertOk()
        ->assertSee('Nuevo Historial');
});

it('lista historiales en DataTable con badges', function () {
    OwnershipHistory::create([
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->owner->id,
        'start_date' => '2024-01-01',
        'acquisition_type' => 'birth',
        'currency' => 'PEN',
        'is_current' => true,
    ]);

    $this->getJson(route('admin.ownership-histories.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.cattle_name', 'Toro Norte')
        ->assertSee('Nacimiento', false)
        ->assertSee('Actual', false);
});

it('crea propietario actual, cierra el anterior y sincroniza ganado', function () {
    $this->post(route('admin.ownership-histories.store'), [
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->owner->id,
        'start_date' => '2024-01-01',
        'acquisition_type' => 'birth',
        'currency' => 'PEN',
        'is_current' => '1',
    ])->assertOk();

    $firstHistory = OwnershipHistory::firstOrFail();
    $this->cattle->refresh();

    expect($firstHistory->is_current)->toBeTrue()
        ->and($this->cattle->current_owner_id)->toBe($this->owner->id);

    $this->post(route('admin.ownership-histories.store'), [
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->secondOwner->id,
        'start_date' => '2024-02-01',
        'acquisition_type' => 'purchase',
        'price' => 2500,
        'currency' => 'USD',
        'is_current' => '1',
    ])->assertOk();

    $firstHistory->refresh();
    $secondHistory = OwnershipHistory::where('owner_id', $this->secondOwner->id)->firstOrFail();
    $this->cattle->refresh();

    expect($firstHistory->is_current)->toBeFalse()
        ->and($firstHistory->end_date->toDateString())->toBe('2024-01-31')
        ->and($secondHistory->is_current)->toBeTrue()
        ->and($secondHistory->end_date)->toBeNull()
        ->and($this->cattle->current_owner_id)->toBe($this->secondOwner->id);
});

it('valida duplicado exacto, fechas y rangos superpuestos', function () {
    OwnershipHistory::create([
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->owner->id,
        'start_date' => '2024-01-01',
        'end_date' => '2024-03-31',
        'acquisition_type' => 'purchase',
        'currency' => 'PEN',
        'is_current' => false,
    ]);

    $this->postJson(route('admin.ownership-histories.store'), [
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->owner->id,
        'start_date' => '2024-01-01',
        'acquisition_type' => 'purchase',
    ])->assertUnprocessable()
        ->assertJsonPath('errors.start_date.0', 'Este propietario ya tiene un historial registrado para este ganado en esa fecha.');

    $this->postJson(route('admin.ownership-histories.store'), [
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->secondOwner->id,
        'start_date' => '2024-03-01',
        'end_date' => '2024-02-01',
        'acquisition_type' => 'transfer',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['end_date']);

    $this->postJson(route('admin.ownership-histories.store'), [
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->secondOwner->id,
        'start_date' => '2024-02-01',
        'end_date' => '2024-04-01',
        'acquisition_type' => 'transfer',
    ])->assertUnprocessable()
        ->assertJsonPath('errors.start_date.0', 'El rango de fechas se superpone con otro historial del mismo ganado.');
});

it('edita muestra detalle y elimina el actual reasignando propietario', function () {
    $first = OwnershipHistory::create([
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->owner->id,
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-31',
        'acquisition_type' => 'birth',
        'currency' => 'PEN',
        'is_current' => false,
    ]);

    $second = OwnershipHistory::create([
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->secondOwner->id,
        'start_date' => '2024-02-01',
        'acquisition_type' => 'purchase',
        'document_reference' => 'DOC-100',
        'price' => 1500,
        'currency' => 'USD',
        'is_current' => true,
    ]);

    $this->cattle->update(['current_owner_id' => $this->secondOwner->id]);

    $this->getJson(route('admin.ownership-histories.show', $second))
        ->assertOk()
        ->assertJsonPath('history.owner_name', 'Ganaderia Sur SAC')
        ->assertJsonPath('history.cattle_code', 'CEBU-000001')
        ->assertJsonPath('history.acquisition_type_label', 'Compra');

    $this->put(route('admin.ownership-histories.update', $first), [
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->owner->id,
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-30',
        'acquisition_type' => 'transfer',
        'currency' => 'PEN',
        'is_current' => '0',
    ])->assertOk();

    $first->refresh();
    expect($first->acquisition_type)->toBe('transfer')
        ->and($first->end_date->toDateString())->toBe('2024-01-30');

    $this->delete(route('admin.ownership-histories.destroy', $second))->assertOk();

    $first->refresh();
    $this->cattle->refresh();

    expect($first->is_current)->toBeTrue()
        ->and($first->end_date)->toBeNull()
        ->and($this->cattle->current_owner_id)->toBe($this->owner->id);
});

it('sincroniza historial al crear y cambiar propietario desde ganado', function () {
    $this->post(route('admin.cattle.store'), [
        'name' => 'Vaca Sincronizada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->owner->id,
        'sex' => 'female',
        'birth_date' => '2024-01-15',
        'status' => 'active',
        'sale_status' => 'not_available',
    ])->assertOk();

    $createdCattle = Cattle::where('name', 'Vaca Sincronizada')->firstOrFail();
    $history = $createdCattle->ownershipHistories()->firstOrFail();

    expect($history->owner_id)->toBe($this->owner->id)
        ->and($history->start_date->toDateString())->toBe('2024-01-15')
        ->and($history->acquisition_type)->toBe('birth')
        ->and($history->is_current)->toBeTrue();

    $this->put(route('admin.cattle.update', $createdCattle), [
        'name' => 'Vaca Sincronizada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->secondOwner->id,
        'sex' => 'female',
        'birth_date' => '2024-01-15',
        'status' => 'active',
        'sale_status' => 'not_available',
    ])->assertOk();

    $createdCattle->refresh();

    expect($createdCattle->current_owner_id)->toBe($this->secondOwner->id)
        ->and($createdCattle->ownershipHistories()->where('is_current', true)->count())->toBe(1)
        ->and($createdCattle->ownershipHistories()->where('owner_id', $this->secondOwner->id)->exists())->toBeTrue();
});
