<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\CattleSale;
use App\Models\Owner;
use App\Models\OwnershipHistory;
use App\Models\Ranch;
use App\Models\User;
use Database\Seeders\CattleSalePermissionSeeder;
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
        'admin.cattle-sales.index',
        'admin.cattle-sales.store',
        'admin.cattle-sales.update',
        'admin.cattle-sales.destroy',
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

    $this->seller = Owner::create([
        'owner_type' => 'company',
        'full_name' => 'Luis Ramos',
        'business_name' => 'Ganaderia Norte SAC',
        'status' => 'active',
    ]);

    $this->buyer = Owner::create([
        'owner_type' => 'person',
        'full_name' => 'Carlos Mendoza',
        'status' => 'active',
    ]);

    $this->otherBuyer = Owner::create([
        'owner_type' => 'person',
        'full_name' => 'Ana Torres',
        'status' => 'active',
    ]);

    $this->cattle = Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Toro Norte',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->seller->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'available',
    ]);

    OwnershipHistory::create([
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->seller->id,
        'start_date' => '2024-01-01',
        'acquisition_type' => 'birth',
        'currency' => 'PEN',
        'is_current' => true,
    ]);
});

it('muestra el modulo de ventas de ganado', function () {
    $this->get(route('admin.cattle-sales.index'))
        ->assertOk()
        ->assertSee('Ventas del Ganado')
        ->assertSee('Nueva Venta')
        ->assertSee('tableCattleSale');
});

it('permite acceder con el rol administrador sembrado', function () {
    $this->seed(CattleSalePermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.cattle-sales.index'))
        ->assertOk()
        ->assertSee('Nueva Venta');
});

it('lista ventas en DataTable con badges', function () {
    CattleSale::create([
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->buyer->id,
        'sale_date' => '2024-04-10',
        'sale_price' => 3500,
        'currency' => 'PEN',
        'payment_method' => 'transfer',
        'status' => 'pending',
    ]);

    $this->getJson(route('admin.cattle-sales.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.cattle_name', 'Toro Norte')
        ->assertSee('Transferencia', false)
        ->assertSee('Pendiente', false);
});

it('guarda venta pendiente con contrato y edita sin perder archivo', function () {
    Storage::fake('public');

    $this->post(route('admin.cattle-sales.store'), [
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->buyer->id,
        'sale_date' => '2024-04-10',
        'sale_price' => 3500,
        'currency' => 'PEN',
        'payment_method' => 'cash',
        'status' => 'pending',
        'contract_file' => UploadedFile::fake()->create('contrato.pdf', 200, 'application/pdf'),
    ])->assertOk();

    $sale = CattleSale::firstOrFail();
    $contractPath = $sale->contract_file_path;

    expect($contractPath)->not->toBeNull();
    Storage::disk('public')->assertExists($contractPath);

    $this->put(route('admin.cattle-sales.update', $sale), [
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->buyer->id,
        'sale_date' => '2024-04-11',
        'sale_price' => 3600,
        'currency' => 'PEN',
        'payment_method' => 'transfer',
        'status' => 'pending',
    ])->assertOk();

    $sale->refresh();

    expect($sale->contract_file_path)->toBe($contractPath)
        ->and($sale->sale_price)->toBe('3600.00');
    Storage::disk('public')->assertExists($contractPath);

    $this->getJson(route('admin.cattle-sales.show', $sale))
        ->assertOk()
        ->assertJsonPath('sale.contract_file_name', basename($contractPath));
});

it('completa venta y sincroniza propietario actual e historial', function () {
    $this->post(route('admin.cattle-sales.store'), [
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->buyer->id,
        'sale_date' => '2024-04-10',
        'sale_price' => 3500,
        'currency' => 'USD',
        'payment_method' => 'transfer',
        'status' => 'completed',
    ])->assertOk();

    $this->cattle->refresh();

    expect($this->cattle->current_owner_id)->toBe($this->buyer->id)
        ->and($this->cattle->sale_status)->toBe('sold');

    $this->assertDatabaseHas('ownership_histories', [
        'cattle_id' => $this->cattle->id,
        'owner_id' => $this->buyer->id,
        'acquisition_type' => 'purchase',
        'is_current' => true,
        'currency' => 'USD',
    ]);

    expect(OwnershipHistory::where('cattle_id', $this->cattle->id)->where('is_current', true)->count())->toBe(1);
});

it('valida comprador distinto y bloquea segunda venta completada', function () {
    $this->postJson(route('admin.cattle-sales.store'), [
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->seller->id,
        'sale_date' => '2024-04-10',
        'sale_price' => 3500,
        'currency' => 'PEN',
        'payment_method' => 'cash',
        'status' => 'completed',
    ])->assertUnprocessable()
        ->assertJsonPath('errors.buyer_owner_id.0', 'El vendedor y el comprador no pueden ser el mismo propietario.');

    CattleSale::create([
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->buyer->id,
        'sale_date' => '2024-04-10',
        'sale_price' => 3500,
        'currency' => 'PEN',
        'payment_method' => 'cash',
        'status' => 'completed',
    ]);

    $this->postJson(route('admin.cattle-sales.store'), [
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->buyer->id,
        'buyer_owner_id' => $this->otherBuyer->id,
        'sale_date' => '2024-05-10',
        'sale_price' => 4000,
        'currency' => 'PEN',
        'payment_method' => 'cash',
        'status' => 'completed',
    ])->assertUnprocessable()
        ->assertJsonPath('errors.status.0', 'Este ganado ya tiene una venta completada registrada.');
});

it('anula venta completada y revierte propietario de forma controlada', function () {
    $sale = CattleSale::create([
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->buyer->id,
        'sale_date' => '2024-04-10',
        'sale_price' => 3500,
        'currency' => 'PEN',
        'payment_method' => 'cash',
        'status' => 'pending',
    ]);

    $this->put(route('admin.cattle-sales.update', $sale), [
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->buyer->id,
        'sale_date' => '2024-04-10',
        'sale_price' => 3500,
        'currency' => 'PEN',
        'payment_method' => 'cash',
        'status' => 'completed',
    ])->assertOk();

    $this->put(route('admin.cattle-sales.update', $sale), [
        'cattle_id' => $this->cattle->id,
        'seller_owner_id' => $this->seller->id,
        'buyer_owner_id' => $this->buyer->id,
        'sale_date' => '2024-04-10',
        'sale_price' => 3500,
        'currency' => 'PEN',
        'payment_method' => 'cash',
        'status' => 'cancelled',
    ])->assertOk();

    $this->cattle->refresh();

    expect($this->cattle->current_owner_id)->toBe($this->seller->id)
        ->and($this->cattle->sale_status)->toBe('available');
});
