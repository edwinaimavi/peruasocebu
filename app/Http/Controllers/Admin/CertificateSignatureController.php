<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CertificateSignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.certificate-signatures.index')->only('index', 'list', 'show', 'listByCertificate');
        $this->middleware('can:admin.certificate-signatures.store')->only('store', 'storeByCertificate');
        $this->middleware('can:admin.certificate-signatures.update')->only('update');
        $this->middleware('can:admin.certificate-signatures.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.certificate_signatures.index', [
            'certificates' => $this->certificateOptions(),
            'certificateTypes' => $this->certificateTypes(),
            'personTypes' => $this->personTypes(),
        ]);
    }

    public function list(): JsonResponse
    {
        $signatures = CertificateSignature::query()
            ->with(['certificate.cattle'])
            ->latest('id');

        return DataTables::eloquent($signatures)
            ->addIndexColumn()
            ->addColumn('certificate_label', fn (CertificateSignature $signature) => $this->certificateLabel($signature->certificate))
            ->editColumn('person_type', fn (CertificateSignature $signature) => $this->personTypeBadge($signature->person_type))
            ->addColumn('signature_badge', fn (CertificateSignature $signature) => $this->fileBadge($signature->signature_path, 'firma'))
            ->addColumn('seal_badge', fn (CertificateSignature $signature) => $this->fileBadge($signature->seal_path, 'sello'))
            ->editColumn('created_at', fn (CertificateSignature $signature) => $signature->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (CertificateSignature $signature) => view(
                'admin.certificate_signatures.partials.acciones',
                compact('signature')
            )->render())
            ->rawColumns(['person_type', 'signature_badge', 'seal_badge', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureSignatureIsUnique($data);
        $files = $this->storeFiles($request);

        try {
            DB::transaction(function () use ($data, $files): void {
                $signature = CertificateSignature::create(array_merge($data, $files));
                $this->regenerateCertificatePdf($signature->certificate);
            });
        } catch (\Throwable $exception) {
            $this->deleteFile($files['signature_path'] ?? null);
            $this->deleteFile($files['seal_path'] ?? null);
            throw $exception;
        }

        return response()->json([
            'message' => 'Firma de certificado registrada correctamente.',
        ]);
    }

    public function show(CertificateSignature $certificateSignature): JsonResponse
    {
        $certificateSignature->load(['certificate.cattle']);

        return response()->json([
            'signature' => $this->signaturePayload($certificateSignature),
        ]);
    }

    public function update(Request $request, CertificateSignature $certificateSignature): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureSignatureIsUnique($data, $certificateSignature);
        $files = $this->storeFiles($request);
        $oldSignaturePath = array_key_exists('signature_path', $files) ? $certificateSignature->signature_path : null;
        $oldSealPath = array_key_exists('seal_path', $files) ? $certificateSignature->seal_path : null;

        try {
            DB::transaction(function () use ($certificateSignature, $data, $files): void {
                $certificateSignature->update(array_merge($data, $files));
                $this->regenerateCertificatePdf($certificateSignature->fresh()->certificate);
            });
        } catch (\Throwable $exception) {
            $this->deleteFile($files['signature_path'] ?? null);
            $this->deleteFile($files['seal_path'] ?? null);
            throw $exception;
        }

        $this->deleteFile($oldSignaturePath);
        $this->deleteFile($oldSealPath);

        return response()->json([
            'message' => 'Firma de certificado actualizada correctamente.',
        ]);
    }

    public function destroy(CertificateSignature $certificateSignature): JsonResponse
    {
        $certificate = $certificateSignature->certificate;
        $signaturePath = $certificateSignature->signature_path;
        $sealPath = $certificateSignature->seal_path;

        DB::transaction(function () use ($certificateSignature, $certificate): void {
            $certificateSignature->delete();
            $this->regenerateCertificatePdf($certificate);
        });

        $this->deleteFile($signaturePath);
        $this->deleteFile($sealPath);

        return response()->json([
            'message' => 'Firma de certificado eliminada correctamente.',
        ]);
    }

    public function listByCertificate(Certificate $certificate): JsonResponse
    {
        return response()->json([
            'signatures' => $certificate->signatures()
                ->latest('id')
                ->get()
                ->map(fn (CertificateSignature $signature) => $this->signaturePayload($signature))
                ->values(),
        ]);
    }

    public function storeByCertificate(Request $request, Certificate $certificate): JsonResponse
    {
        $request->merge(['certificate_id' => $certificate->id]);

        return $this->store($request);
    }

    public function create() {}

    public function edit(CertificateSignature $certificateSignature) {}

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'certificate_id' => ['required', 'exists:certificates,id'],
            'person_type' => ['required', Rule::in(array_keys($this->personTypes()))],
            'person_name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'signature_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'seal_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'certificate_id.required' => 'Seleccione el certificado.',
            'certificate_id.exists' => 'El certificado seleccionado no es valido.',
            'person_type.required' => 'Seleccione el tipo de persona.',
            'person_type.in' => 'El tipo de persona seleccionado no es valido.',
            'person_name.required' => 'Ingrese el nombre de la persona.',
            'person_name.max' => 'El nombre no debe superar los 255 caracteres.',
            'signature_file.image' => 'La firma debe ser una imagen valida.',
            'signature_file.mimes' => 'La firma debe ser JPG, PNG o WEBP.',
            'signature_file.max' => 'La firma no debe superar los 4 MB.',
            'seal_file.image' => 'El sello debe ser una imagen valida.',
            'seal_file.mimes' => 'El sello debe ser JPG, PNG o WEBP.',
            'seal_file.max' => 'El sello no debe superar los 4 MB.',
        ]);
    }

    private function ensureSignatureIsUnique(array $data, ?CertificateSignature $ignore = null): void
    {
        $exists = CertificateSignature::query()
            ->where('certificate_id', $data['certificate_id'])
            ->where('person_type', $data['person_type'])
            ->where('person_name', $data['person_name'])
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'person_name' => 'Esta persona ya tiene una firma registrada para este certificado.',
            ]);
        }
    }

    private function storeFiles(Request $request): array
    {
        $files = [];

        if ($request->hasFile('signature_file')) {
            $files['signature_path'] = $request->file('signature_file')->store('certificates/signatures', 'public');
        }

        if ($request->hasFile('seal_file')) {
            $files['seal_path'] = $request->file('seal_file')->store('certificates/seals', 'public');
        }

        return $files;
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function regenerateCertificatePdf(?Certificate $certificate): void
    {
        if (! $certificate) {
            return;
        }

        $certificate->load(['cattle.breed', 'ranch', 'owner', 'veterinarian', 'signatures']);
        $path = 'certificates/pdfs/'.$certificate->certificate_number.'.pdf';
        $absolutePath = Storage::disk('public')->path($path);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0775, true);
        }

        Pdf::loadView('admin.certificates.pdf', [
            'certificate' => $certificate,
            'typeLabel' => $this->certificateTypeLabel($certificate->certificate_type),
            'statusLabel' => $this->certificateStatusLabel($certificate->status),
            'qrPath' => $certificate->qr_code_path && Storage::disk('public')->exists($certificate->qr_code_path)
                ? Storage::disk('public')->path($certificate->qr_code_path)
                : null,
            'verifyUrl' => route('certificates.verify', $certificate->verification_code),
        ])->setPaper('a4')->save($absolutePath);

        $certificate->updateQuietly(['pdf_path' => $path]);
    }

    private function certificateOptions()
    {
        return Certificate::with('cattle')
            ->latest('id')
            ->get();
    }

    private function signaturePayload(CertificateSignature $signature): array
    {
        $signature->loadMissing(['certificate.cattle']);

        return array_merge($signature->toArray(), [
            'certificate_label' => $this->certificateLabel($signature->certificate),
            'certificate_number' => $signature->certificate?->certificate_number,
            'verification_code' => $signature->certificate?->verification_code,
            'cattle_label' => $signature->certificate?->cattle
                ? trim($signature->certificate->cattle->code.' - '.($signature->certificate->cattle->name ?: 'Sin nombre'))
                : '-',
            'certificate_type_label' => $this->certificateTypeLabel($signature->certificate?->certificate_type),
            'certificate_status_label' => $this->certificateStatusLabel($signature->certificate?->status),
            'person_type_label' => $this->personTypeLabel($signature->person_type),
            'signature_url' => $this->fileUrl($signature->signature_path),
            'seal_url' => $this->fileUrl($signature->seal_path),
            'created_at_formatted' => $signature->created_at?->format('d/m/Y H:i'),
            'updated_at_formatted' => $signature->updated_at?->format('d/m/Y H:i'),
        ]);
    }

    private function certificateLabel(?Certificate $certificate): string
    {
        if (! $certificate) {
            return '-';
        }

        return trim($certificate->certificate_number.' - '.($certificate->cattle?->code ?: 'Sin ganado').' - '.$this->certificateTypeLabel($certificate->certificate_type));
    }

    private function personTypes(): array
    {
        return [
            'owner' => 'Dueno',
            'veterinarian' => 'Veterinario',
            'representative' => 'Representante',
            'certifier' => 'Certificador',
            'other' => 'Otro',
        ];
    }

    private function personTypeLabel(?string $type): string
    {
        return $this->personTypes()[$type] ?? '-';
    }

    private function personTypeBadge(?string $type): string
    {
        $classes = [
            'owner' => 'badge-warning',
            'veterinarian' => 'badge-success',
            'representative' => 'badge-primary',
            'certifier' => 'badge-info',
            'other' => 'badge-secondary',
        ];

        return '<span class="badge '.($classes[$type] ?? 'badge-secondary').'">'.$this->personTypeLabel($type).'</span>';
    }

    private function fileBadge(?string $path, string $label): string
    {
        return $path
            ? '<span class="badge badge-success">Con '.$label.'</span>'
            : '<span class="badge badge-secondary">Sin '.$label.'</span>';
    }

    private function certificateTypeLabel(?string $type): string
    {
        return $this->certificateTypes()[$type] ?? '-';
    }

    private function certificateTypes(): array
    {
        return [
            'breed' => 'Raza',
            'genealogy' => 'Genealogia',
            'ownership' => 'Propiedad',
            'purity' => 'Pureza',
        ];
    }

    private function certificateStatusLabel(?string $status): string
    {
        return match ($status) {
            'issued' => 'Emitido',
            'cancelled' => 'Anulado',
            'expired' => 'Vencido',
            default => '-',
        };
    }

    private function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
