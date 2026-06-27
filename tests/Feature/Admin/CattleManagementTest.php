<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\CattleGenealogyLink;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\User;
use Database\Seeders\CattlePermissionSeeder;
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
});

it('muestra el modulo de ganado', function () {
    $this->get(route('admin.cattle.index'))
        ->assertOk()
        ->assertSee('Ganado')
        ->assertSee('Nuevo Ganado')
        ->assertSee('tableCattle');
});

it('permite acceder al modulo con el rol administrador sembrado', function () {
    $this->seed(CattlePermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.cattle.index'))
        ->assertOk()
        ->assertSee('Nuevo Ganado');
});

it('devuelve el listado para DataTable', function () {
    Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Toro Norte',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->owner->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'available',
        'is_public' => true,
    ]);

    $this->getJson(route('admin.cattle.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertJsonPath('data.0.name', 'Toro Norte')
        ->assertSee('CEBU-000001', false)
        ->assertSee('Disponible', false);
});

it('crea ganado con codigo automatico y evita duplicados', function () {
    Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Toro Antiguo',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->post(route('admin.cattle.store'), [
        'code' => 'CEBU-000001',
        'name' => 'Toro Nuevo',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->owner->id,
        'sex' => 'male',
        'birth_date' => '2024-01-15',
        'purity_percentage' => '98.5',
        'status' => 'active',
        'sale_status' => 'available',
        'is_public' => '1',
    ])->assertOk()
        ->assertJson(['message' => 'Ganado registrado correctamente.']);

    $cattle = Cattle::where('name', 'Toro Nuevo')->firstOrFail();

    expect($cattle->code)->toBe('CEBU-000002')
        ->and($cattle->is_public)->toBeTrue();
});

it('sincroniza padre y madre hacia genealogia al crear y actualizar ganado', function () {
    $father = Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Padre Registrado',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'purity_percentage' => 97,
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $mother = Cattle::create([
        'code' => 'CEBU-000002',
        'name' => 'Madre Registrada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'purity_percentage' => 96,
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->post(route('admin.cattle.store'), [
        'name' => 'Cria Sincronizada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'father_id' => $father->id,
        'mother_id' => $mother->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ])->assertOk();

    $child = Cattle::where('name', 'Cria Sincronizada')->firstOrFail();

    $this->assertDatabaseHas('cattle_genealogy_links', [
        'cattle_id' => $child->id,
        'relative_cattle_id' => $father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'relative_code' => 'CEBU-000001',
        'relative_name' => 'Padre Registrado',
    ]);

    $this->assertDatabaseHas('cattle_genealogy_links', [
        'cattle_id' => $child->id,
        'relative_cattle_id' => $mother->id,
        'relation_type' => 'mother',
        'generation_level' => 1,
        'relative_code' => 'CEBU-000002',
        'relative_name' => 'Madre Registrada',
    ]);

    $newFather = Cattle::create([
        'code' => 'CEBU-000010',
        'name' => 'Padre Nuevo',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->put(route('admin.cattle.update', $child), [
        'name' => 'Cria Sincronizada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'father_id' => $newFather->id,
        'mother_id' => $mother->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ])->assertOk();

    expect(CattleGenealogyLink::where('cattle_id', $child->id)->where('relation_type', 'father')->count())->toBe(1);

    $this->assertDatabaseHas('cattle_genealogy_links', [
        'cattle_id' => $child->id,
        'relative_cattle_id' => $newFather->id,
        'relation_type' => 'father',
        'relative_name' => 'Padre Nuevo',
    ]);
});

it('quita el enlace genealogico automatico al retirar padre desde ganado', function () {
    $father = Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Padre Registrado',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $child = Cattle::create([
        'code' => 'CEBU-000002',
        'name' => 'Cria con Padre',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'father_id' => $father->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    CattleGenealogyLink::create([
        'cattle_id' => $child->id,
        'relative_cattle_id' => $father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'relative_code' => $father->code,
        'relative_name' => $father->name,
    ]);

    $this->put(route('admin.cattle.update', $child), [
        'name' => 'Cria con Padre',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'father_id' => '',
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ])->assertOk();

    $child->refresh();

    expect($child->father_id)->toBeNull();
    $this->assertDatabaseMissing('cattle_genealogy_links', [
        'cattle_id' => $child->id,
        'relation_type' => 'father',
    ]);
});

it('sube, conserva y reemplaza la foto principal', function () {
    Storage::fake('public');

    $this->post(route('admin.cattle.store'), [
        'name' => 'Vaca Foto',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
        'main_photo' => UploadedFile::fake()->image('ganado.jpg'),
    ])->assertOk();

    $cattle = Cattle::firstOrFail();
    $firstPhoto = $cattle->main_photo_path;

    expect($firstPhoto)->not->toBeNull();
    Storage::disk('public')->assertExists($firstPhoto);

    $this->post(route('admin.cattle.update', $cattle), [
        '_method' => 'PUT',
        'name' => 'Vaca Foto Actualizada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'reserved',
    ])->assertOk();

    $cattle->refresh();

    expect($cattle->main_photo_path)->toBe($firstPhoto);
    Storage::disk('public')->assertExists($firstPhoto);

    $this->post(route('admin.cattle.update', $cattle), [
        '_method' => 'PUT',
        'name' => 'Vaca Foto Actualizada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'reserved',
        'main_photo' => UploadedFile::fake()->image('ganado-nuevo.webp'),
    ])->assertOk();

    $cattle->refresh();

    expect($cattle->main_photo_path)->not->toBe($firstPhoto);
    Storage::disk('public')->assertMissing($firstPhoto);
    Storage::disk('public')->assertExists($cattle->main_photo_path);
});

it('devuelve detalle con genealogia basica y elimina mediante soft delete', function () {
    $father = Cattle::create([
        'code' => 'CEBU-000001',
        'name' => 'Padre Registrado',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $mother = Cattle::create([
        'code' => 'CEBU-000002',
        'name' => 'Madre Registrada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $child = Cattle::create([
        'code' => 'CEBU-000003',
        'name' => 'Cria Registrada',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->owner->id,
        'father_id' => $father->id,
        'mother_id' => $mother->id,
        'sex' => 'female',
        'birth_date' => '2024-01-15',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->getJson(route('admin.cattle.show', $child))
        ->assertOk()
        ->assertJsonPath('cattle.name', 'Cria Registrada')
        ->assertJsonPath('cattle.birth_date', '2024-01-15')
        ->assertJsonPath('cattle.birth_date_formatted', '15/01/2024')
        ->assertJsonPath('cattle.father_label', 'CEBU-000001 - Padre Registrado')
        ->assertJsonPath('cattle.mother_label', 'CEBU-000002 - Madre Registrada')
        ->assertJsonPath('cattle.owner_name', 'Carlos Mendoza');

    $ageText = $this->getJson(route('admin.cattle.show', $child))->json('cattle.age_text');

    expect($ageText)
        ->toContain("a\u{00F1}os")
        ->not->toContain('Ã');

    $this->delete(route('admin.cattle.destroy', $child))
        ->assertOk()
        ->assertJson(['message' => 'Ganado eliminado correctamente.']);

    $this->assertSoftDeleted('cattle', ['id' => $child->id]);
});

it('muestra padre manual en el detalle si no hay father_id', function () {
    $child = Cattle::create([
        'code' => 'CEBU-000003',
        'name' => 'Cria Manual',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    CattleGenealogyLink::create([
        'cattle_id' => $child->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'relative_code' => 'MAN-001',
        'relative_name' => 'Padre Manual',
        'breed_id' => $this->breed->id,
    ]);

    $this->getJson(route('admin.cattle.show', $child))
        ->assertOk()
        ->assertJsonPath('cattle.father_label', 'MAN-001 - Padre Manual')
        ->assertJsonPath('cattle.father_breed_name', 'Cebu');
});

it('valida relaciones, sexo, estado y medidas', function () {
    $this->postJson(route('admin.cattle.store'), [
        'name' => '',
        'breed_id' => '',
        'ranch_id' => '',
        'sex' => 'otro',
        'weight_kg' => '-10',
        'height_cm' => '-1',
        'purity_percentage' => '101',
        'status' => '',
        'sale_status' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors([
            'name',
            'breed_id',
            'ranch_id',
            'sex',
            'weight_kg',
            'height_cm',
            'purity_percentage',
            'status',
            'sale_status',
        ]);
});
