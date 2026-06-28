<?php

use App\Models\Breed;
use App\Models\Cattle;
use App\Models\CattleGenealogyLink;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\User;
use Database\Seeders\CattleGenealogyPermissionSeeder;
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
        'admin.cattle-genealogy.index',
        'admin.cattle-genealogy.store',
        'admin.cattle-genealogy.update',
        'admin.cattle-genealogy.destroy',
    ])->each(function (string $permission) {
        Permission::create(['name' => $permission]);
    });

    $this->user->givePermissionTo(Permission::all());
    $this->actingAs($this->user);

    $this->breed = Breed::create([
        'name' => 'Cebu',
        'code' => 'CE',
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

    $this->mainCattle = Cattle::create([
        'code' => 'CE-000001',
        'name' => 'Romulo',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'current_owner_id' => $this->owner->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->father = Cattle::create([
        'code' => 'CE-000002',
        'name' => 'Toro Supremo',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'purity_percentage' => 98.5,
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);
});

it('muestra el modulo de genealogia', function () {
    $this->get(route('admin.cattle-genealogy.index'))
        ->assertOk()
        ->assertSee('Genealogía del Ganado')
        ->assertSee('Nuevo Registro Genealógico')
        ->assertSee('Animal hijo / animal principal')
        ->assertSee('Familiar que será asignado')
        ->assertSee('data-sex="male"', false)
        ->assertSee('tableGenealogy');
});

it('permite acceder con el rol administrador sembrado', function () {
    $this->seed(CattleGenealogyPermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Role::where('name', 'Administrador')->firstOrFail());

    $this->actingAs($admin)
        ->get(route('admin.cattle-genealogy.index'))
        ->assertOk()
        ->assertSee('Nuevo Registro Genealógico');
});

it('crea un registro con familiar registrado y sincroniza padre si esta vacio', function () {
    $this->post(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'notes' => 'Padre confirmado.',
    ])->assertOk()
        ->assertJson(['message' => 'Registro genealógico guardado correctamente.']);

    $link = CattleGenealogyLink::firstOrFail();

    expect($link->relative_code)->toBe('CE-000002')
        ->and($link->relative_name)->toBe('Toro Supremo')
        ->and((float) $link->purity_percentage)->toBe(98.5);

    $this->mainCattle->refresh();
    expect($this->mainCattle->father_id)->toBe($this->father->id);
});

it('bloquea duplicado principal y conflicto con padre existente al crear genealogia', function () {
    CattleGenealogyLink::create([
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'relative_code' => $this->father->code,
        'relative_name' => $this->father->name,
    ]);

    $otherFather = Cattle::create([
        'code' => 'CE-000003',
        'name' => 'Toro Alterno',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $otherFather->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['relation_type'])
        ->assertJsonPath('errors.relation_type.0', 'Este animal ya tiene un padre registrado.');

    $secondCattle = Cattle::create([
        'code' => 'CE-000004',
        'name' => 'Cria con padre directo',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'father_id' => $this->father->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $secondCattle->id,
        'relative_cattle_id' => $otherFather->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['relative_cattle_id'])
        ->assertJsonPath('errors.relative_cattle_id.0', 'Este animal ya tiene un padre registrado. Edite el registro existente si desea cambiarlo.');
});

it('permite cambiar padre desde update de genealogia y valida sexo', function () {
    $mother = Cattle::create([
        'code' => 'CE-000003',
        'name' => 'Vaca Suprema',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $link = CattleGenealogyLink::create([
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'relative_code' => $this->father->code,
        'relative_name' => $this->father->name,
    ]);

    $this->mainCattle->update(['father_id' => $this->father->id]);

    $this->putJson(route('admin.cattle-genealogy.update', $link), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $mother->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['relative_cattle_id'])
        ->assertJsonPath('errors.relative_cattle_id.0', 'El padre debe ser un animal macho.');

    $newFather = Cattle::create([
        'code' => 'CE-000004',
        'name' => 'Toro Nuevo',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->putJson(route('admin.cattle-genealogy.update', $link), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $newFather->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertOk();

    $this->mainCattle->refresh();
    $link->refresh();

    expect($this->mainCattle->father_id)->toBe($newFather->id)
        ->and($link->relative_cattle_id)->toBe($newFather->id)
        ->and($link->relative_name)->toBe('Toro Nuevo');
});

it('asigna madre registrada y muestra mensaje claro cuando el familiar es macho', function () {
    $mother = Cattle::create([
        'code' => 'GY001-000003',
        'name' => 'Bella Gyr',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'female',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $mother->id,
        'relation_type' => 'mother',
        'generation_level' => 1,
    ])->assertOk()
        ->assertJson(['message' => 'Registro genealógico guardado correctamente.']);

    $this->mainCattle->refresh();
    expect($this->mainCattle->mother_id)->toBe($mother->id);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $mother->id,
        'relative_cattle_id' => $this->mainCattle->id,
        'relation_type' => 'mother',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['relative_cattle_id'])
        ->assertJsonPath('errors.relative_cattle_id.0', 'La madre debe ser un animal hembra.');
});

it('bloquea familiar repetido como padre y abuelo del mismo animal', function () {
    $grandfather = Cattle::create([
        'code' => 'CE-000030',
        'name' => 'Padre Real de Toro Supremo',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'birth_date' => '2010-01-01',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->father->update(['birth_date' => '2014-01-01']);
    $this->mainCattle->update([
        'father_id' => $this->father->id,
        'birth_date' => '2017-01-01',
    ]);

    CattleGenealogyLink::create([
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'relative_code' => $this->father->code,
        'relative_name' => $this->father->name,
    ]);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'paternal_grandfather',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonPath('errors.relative_cattle_id.0', 'El padre del animal no puede registrarse también como abuelo paterno.');

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $grandfather->id,
        'relation_type' => 'paternal_grandfather',
        'generation_level' => 1,
    ])->assertOk();

    $link = CattleGenealogyLink::where('cattle_id', $this->mainCattle->id)
        ->where('relation_type', 'paternal_grandfather')
        ->firstOrFail();

    expect($link->relative_cattle_id)->toBe($grandfather->id)
        ->and($link->generation_level)->toBe(2);
});

it('bloquea el mismo familiar registrado en otra relacion genealogica', function () {
    $relative = Cattle::create([
        'code' => 'CE-000031',
        'name' => 'Familiar Repetido',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'birth_date' => '2010-01-01',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->mainCattle->update(['birth_date' => '2017-01-01']);

    CattleGenealogyLink::create([
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $relative->id,
        'relation_type' => 'maternal_grandfather',
        'generation_level' => 2,
        'relative_code' => $relative->code,
        'relative_name' => $relative->name,
    ]);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $relative->id,
        'relation_type' => 'paternal_grandfather',
        'generation_level' => 2,
    ])->assertUnprocessable()
        ->assertJsonPath('errors.relative_cattle_id.0', 'Este familiar ya está registrado en otra relación genealógica para este animal.');
});

it('valida fecha duplicados y genealogia circular desde genealogia', function () {
    $youngFather = Cattle::create([
        'code' => 'CE-000020',
        'name' => 'Padre Joven',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'birth_date' => '2019-12-26',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->mainCattle->update(['birth_date' => '2017-10-26']);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $youngFather->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonPath('errors.relative_cattle_id.0', 'La fecha de nacimiento del padre debe ser anterior a la fecha de nacimiento del hijo.');

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertOk();

    $otherFather = Cattle::create([
        'code' => 'CE-000021',
        'name' => 'Otro Padre',
        'breed_id' => $this->breed->id,
        'ranch_id' => $this->ranch->id,
        'sex' => 'male',
        'birth_date' => '2010-01-01',
        'status' => 'active',
        'sale_status' => 'not_available',
    ]);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $otherFather->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonPath('errors.relation_type.0', 'Este animal ya tiene un padre registrado.');

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->father->id,
        'relative_cattle_id' => $this->mainCattle->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonPath('errors.relative_cattle_id.0', 'No se puede asignar este familiar porque generaría una genealogía circular.');
});

it('crea, lista, actualiza y elimina un familiar manual', function () {
    $this->post(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relation_type' => 'paternal_grandfather',
        'generation_level' => 2,
        'relative_code' => 'MAN-001',
        'relative_name' => 'Abuelo Manual',
        'breed_id' => $this->breed->id,
        'purity_percentage' => 87.25,
        'notes' => 'Dato histórico.',
    ])->assertOk();

    $link = CattleGenealogyLink::firstOrFail();

    $this->getJson(route('admin.cattle-genealogy.list', [
        'draw' => 1,
        'start' => 0,
        'length' => 10,
    ]))
        ->assertOk()
        ->assertJsonPath('recordsTotal', 1)
        ->assertSee('Abuelo Manual', false)
        ->assertSee('Abuelo paterno', false);

    $this->put(route('admin.cattle-genealogy.update', $link), [
        'cattle_id' => $this->mainCattle->id,
        'relation_type' => 'paternal_grandfather',
        'generation_level' => 2,
        'relative_code' => 'MAN-002',
        'relative_name' => 'Abuelo Manual Actualizado',
        'breed_id' => $this->breed->id,
        'purity_percentage' => 90,
    ])->assertOk()
        ->assertJson(['message' => 'Registro genealógico actualizado correctamente.']);

    $link->refresh();
    expect($link->relative_name)->toBe('Abuelo Manual Actualizado');

    $this->delete(route('admin.cattle-genealogy.destroy', $link))
        ->assertOk()
        ->assertJson(['message' => 'Registro genealógico eliminado correctamente.']);

    $this->assertDatabaseMissing('cattle_genealogy_links', ['id' => $link->id]);
});

it('devuelve detalle completo', function () {
    $link = CattleGenealogyLink::create([
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'relative_code' => $this->father->code,
        'relative_name' => $this->father->name,
        'breed_id' => $this->breed->id,
    ]);

    $this->getJson(route('admin.cattle-genealogy.show', $link))
        ->assertOk()
        ->assertJsonPath('genealogy.cattle_label', 'CE-000001 - Romulo')
        ->assertJsonPath('genealogy.relation_label', 'Padre')
        ->assertJsonPath('genealogy.relative_display_name', 'CE-000002 - Toro Supremo')
        ->assertJsonPath('genealogy.cattle_owner_name', 'Carlos Mendoza');
});

it('valida familiar distinto, campos obligatorios y duplicados', function () {
    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->mainCattle->id,
        'relation_type' => '',
        'generation_level' => '',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['relative_cattle_id', 'relation_type', 'generation_level']);

    CattleGenealogyLink::create([
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
        'relative_code' => $this->father->code,
        'relative_name' => $this->father->name,
    ]);

    $this->postJson(route('admin.cattle-genealogy.store'), [
        'cattle_id' => $this->mainCattle->id,
        'relative_cattle_id' => $this->father->id,
        'relation_type' => 'father',
        'generation_level' => 1,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['relation_type'])
        ->assertJsonPath('errors.relation_type.0', 'Este animal ya tiene un padre registrado.');
});
