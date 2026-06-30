<?php

namespace App\Http\Controllers;

use App\Models\Cattle;
use App\Models\Certificate;
use App\Services\CattleGenealogyTreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicSearchController extends Controller
{
    public function search(Request $request, CattleGenealogyTreeService $genealogyTreeService): View
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['cattle_code', 'certificate_number', 'verification_code'])],
            'q' => ['required', 'string', 'max:100'],
        ], [
            'type.required' => 'Seleccione el tipo de consulta.',
            'type.in' => 'El tipo de consulta no es valido.',
            'q.required' => 'Ingrese un termino de busqueda.',
            'q.max' => 'La busqueda no debe superar los 100 caracteres.',
        ]);

        $query = trim($data['q']);
        $type = $data['type'];

        if ($query === '') {
            return $this->notFound($type, $query, 'Ingrese un termino valido para realizar la consulta.');
        }

        if ($type === 'cattle_code') {
            $cattleQuery = Cattle::with([
                'breed',
                'ranch',
                'currentOwner',
                'father.breed',
                'mother.breed',
                'certificates',
                'photos',
                'genealogyLinks.breed',
                'genealogyLinks.relativeCattle.breed',
            ])->where('code', $query);

            if (Schema::hasColumn('cattle', 'is_public')) {
                $cattleQuery->where('is_public', true);
            }

            $cattle = $cattleQuery->first();

            if (! $cattle) {
                return $this->notFound($type, $query, 'No se encontro ganado publico con ese codigo.');
            }

            return view('public.search.cattle', [
                'cattle' => $cattle,
                'genealogyTree' => $genealogyTreeService->build($cattle),
                'query' => $query,
            ]);
        }

        $certificateQuery = Certificate::with([
            'cattle',
            'cattle.breed',
            'ranch',
            'owner',
            'veterinarian',
        ]);

        if ($type === 'certificate_number') {
            $certificate = $certificateQuery->where('certificate_number', $query)->first();

            if (! $certificate) {
                return $this->notFound($type, $query, 'No se encontro ningun certificado con ese numero.');
            }

            return view('public.search.certificate', [
                'certificate' => $certificate,
                'query' => $query,
            ]);
        }

        $certificate = $certificateQuery->where('verification_code', $query)->first();

        if (! $certificate) {
            return $this->notFound($type, $query, 'No se encontro ningun certificado con ese codigo de verificacion.');
        }

        return view('public.search.certificate', [
            'certificate' => $certificate,
            'query' => $query,
        ]);
    }

    private function notFound(string $type, string $query, string $message): View
    {
        return view('public.search.results', [
            'type' => $type,
            'query' => $query,
            'result' => null,
            'message' => $message,
        ]);
    }
}
