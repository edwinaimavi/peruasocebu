<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Permission::create(['name' => 'admin.ranches.index']);
    $this->user->givePermissionTo('admin.ranches.index');
    $this->actingAs($this->user);

    config([
        'services.apis_net_pe.token' => 'token-de-prueba',
        'services.apis_net_pe.referer' => 'http://peruasocebu.test',
    ]);
});

it('consulta un DNI mediante apis.net.pe', function () {
    Http::fake([
        'api.apis.net.pe/v2/reniec/dni*' => Http::response([
            'numeroDocumento' => '12345678',
            'nombres' => 'JUAN',
            'apellidoPaterno' => 'PÉREZ',
            'apellidoMaterno' => 'ROJAS',
        ]),
    ]);

    $this->getJson(route('admin.documents.consult', '12345678'))
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('type', 'DNI')
        ->assertJsonPath('data.numeroDocumento', '12345678');

    Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer token-de-prueba')
        && $request->hasHeader('Referer', 'http://peruasocebu.test')
        && $request->url() === 'https://api.apis.net.pe/v2/reniec/dni?numero=12345678');
});

it('consulta un RUC mediante apis.net.pe', function () {
    Http::fake([
        'api.apis.net.pe/v2/sunat/ruc*' => Http::response([
            'numeroDocumento' => '20123456789',
            'razonSocial' => 'GANADERÍA DE PRUEBA SAC',
            'direccion' => 'AV. PRINCIPAL 123',
        ]),
    ]);

    $this->getJson(route('admin.documents.consult', '20123456789'))
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('type', 'RUC')
        ->assertJsonPath('data.razonSocial', 'GANADERÍA DE PRUEBA SAC');
});

it('permite consultar documentos con permiso de propietarios', function () {
    $ownerUser = User::factory()->create();
    Permission::create(['name' => 'admin.owners.index']);
    $ownerUser->givePermissionTo('admin.owners.index');
    $this->actingAs($ownerUser);

    Http::fake([
        'api.apis.net.pe/v2/reniec/dni*' => Http::response([
            'numeroDocumento' => '12345678',
            'nombreCompleto' => 'JUAN PEREZ ROJAS',
        ]),
    ]);

    $this->getJson(route('admin.documents.consult', '12345678'))
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('type', 'DNI');
});

it('rechaza documentos con letras o longitud inválida', function (string $number, string $message) {
    Http::fake();

    $this->getJson(route('admin.documents.consult', $number))
        ->assertUnprocessable()
        ->assertJson([
            'status' => false,
            'message' => $message,
        ]);

    Http::assertNothingSent();
})->with([
    ['1234ABCD', 'El número de documento debe contener solo números.'],
    ['123456789', 'El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.'],
]);

it('devuelve 404 cuando el documento no existe', function () {
    Http::fake([
        'api.apis.net.pe/*' => Http::response(['message' => 'No encontrado'], 404),
    ]);

    $this->getJson(route('admin.documents.consult', '12345678'))
        ->assertNotFound()
        ->assertJson([
            'status' => false,
            'message' => 'Documento no encontrado.',
        ]);
});

it('controla errores del servicio externo', function () {
    Http::fake([
        'api.apis.net.pe/*' => Http::response([], 500),
    ]);

    $this->getJson(route('admin.documents.consult', '12345678'))
        ->assertServiceUnavailable()
        ->assertJson([
            'status' => false,
            'message' => 'No se pudo conectar con el servicio de consulta. Intente nuevamente.',
        ]);
});
