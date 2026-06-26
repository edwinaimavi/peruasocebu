<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DocumentLookupController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            $allowedPermissions = [
                'admin.ranches.index',
                'admin.owners.index',
            ];

            foreach ($allowedPermissions as $permission) {
                if ($request->user()?->can($permission)) {
                    return $next($request);
                }
            }

            abort(Response::HTTP_FORBIDDEN);
        })->only('consultarDocumento');
    }

    public function consultarDocumento(string $numero): JsonResponse
    {
        if (! preg_match('/^\d+$/', $numero)) {
            return $this->error('El número de documento debe contener solo números.', 422);
        }

        $type = match (strlen($numero)) {
            8 => 'DNI',
            11 => 'RUC',
            default => null,
        };

        if (! $type) {
            return $this->error('El documento debe tener 8 dígitos para DNI o 11 dígitos para RUC.', 422);
        }

        $token = config('services.apis_net_pe.token');

        if (! $token) {
            Log::warning('No se configuró el token de apis.net.pe.');

            return $this->serviceUnavailable();
        }

        $endpoint = $type === 'DNI'
            ? 'https://api.apis.net.pe/v2/reniec/dni'
            : 'https://api.apis.net.pe/v2/sunat/ruc';

        try {
            $response = Http::acceptJson()
                ->withToken($token)
                ->withHeaders([
                    'Referer' => config('services.apis_net_pe.referer', config('app.url')),
                ])
                ->timeout(10)
                ->get($endpoint, ['numero' => $numero]);
        } catch (ConnectionException $exception) {
            Log::warning('Falló la conexión con apis.net.pe.', [
                'type' => $type,
                'exception' => $exception->getMessage(),
            ]);

            return $this->serviceUnavailable();
        }

        if (in_array($response->status(), [404, 422], true)) {
            return $this->error('Documento no encontrado.', 404);
        }

        if ($response->failed()) {
            Log::warning('apis.net.pe devolvió una respuesta no exitosa.', [
                'type' => $type,
                'status' => $response->status(),
            ]);

            return $this->serviceUnavailable();
        }

        $data = $response->json();

        if (! is_array($data) || $data === []) {
            return $this->error('Documento no encontrado.', 404);
        }

        return response()->json([
            'status' => true,
            'type' => $type,
            'data' => $data,
        ]);
    }

    private function serviceUnavailable(): JsonResponse
    {
        return $this->error(
            'No se pudo conectar con el servicio de consulta. Intente nuevamente.',
            503
        );
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $status);
    }
}
