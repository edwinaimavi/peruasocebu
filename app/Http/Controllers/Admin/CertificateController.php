<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\Certificate;
use App\Models\CertificateSignature;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\Veterinarian;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\Facades\DataTables;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.certificates.index')->only('index', 'list', 'show', 'cattleInfo', 'downloadPdf');
        $this->middleware('can:admin.certificates.store')->only('store', 'regeneratePdf');
        $this->middleware('can:admin.certificates.update')->only('update', 'cancel');
        $this->middleware('can:admin.certificates.destroy')->only('destroy');
    }

    public function index(): View
    {
        $cattle = Cattle::with(['breed', 'ranch', 'currentOwner'])
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        return view('admin.certificates.index', [
            'cattle' => $cattle,
            'ranches' => Ranch::where('status', 'active')->orderBy('name')->get(),
            'owners' => Owner::where('status', 'active')->orderBy('full_name')->get(),
            'veterinarians' => Veterinarian::where('status', 'active')->orderBy('full_name')->get(),
            'certificateTypes' => $this->certificateTypes(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function list(): JsonResponse
    {
        $certificates = Certificate::query()
            ->with(['cattle', 'ranch', 'owner', 'veterinarian'])
            ->latest('id');

        return DataTables::eloquent($certificates)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (Certificate $certificate) => $certificate->cattle?->name ?: '-')
            ->addColumn('cattle_code', fn (Certificate $certificate) => $certificate->cattle?->code ?: '-')
            ->addColumn('owner_name', fn (Certificate $certificate) => $this->ownerDisplayName($certificate->owner) ?: '-')
            ->addColumn('ranch_name', fn (Certificate $certificate) => $this->ranchDisplayName($certificate->ranch) ?: '-')
            ->editColumn('certificate_type', fn (Certificate $certificate) => $this->typeBadge($certificate->certificate_type))
            ->editColumn('purity_percentage', fn (Certificate $certificate) => $certificate->purity_percentage !== null ? number_format((float) $certificate->purity_percentage, 2).'%' : '-')
            ->editColumn('issue_date', fn (Certificate $certificate) => $certificate->issue_date?->format('d/m/Y') ?: '-')
            ->editColumn('status', fn (Certificate $certificate) => $this->statusBadge($certificate->status))
            ->addColumn('acciones', fn (Certificate $certificate) => view(
                'admin.certificates.partials.acciones',
                compact('certificate')
            )->render())
            ->rawColumns(['certificate_type', 'status', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $certificate = null;

        DB::transaction(function () use ($data, &$certificate): void {
            $certificate = Certificate::create(array_merge($data, [
                'certificate_number' => $this->generateCertificateNumber(),
                'verification_code' => $this->generateVerificationCode(),
            ]));

            $this->generateQr($certificate);
            $this->generatePdf($certificate);
        });

        return response()->json([
            'message' => 'Certificado registrado correctamente.',
            'certificate' => ['id' => $certificate?->id],
        ]);
    }

    public function show(Certificate $certificate): JsonResponse
    {
        $certificate->load(['cattle.breed', 'cattle.ranch', 'cattle.currentOwner', 'ranch', 'owner', 'veterinarian', 'signatures']);

        return response()->json([
            'certificate' => $this->certificatePayload($certificate),
        ]);
    }

    public function update(Request $request, Certificate $certificate): JsonResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($certificate, $data): void {
            $certificate->update($data);
            $certificate->refresh();

            if (! $certificate->qr_code_path) {
                $this->generateQr($certificate);
            }

            $this->generatePdf($certificate);
        });

        return response()->json([
            'message' => 'Certificado actualizado correctamente.',
        ]);
    }

    public function destroy(Certificate $certificate): JsonResponse
    {
        $certificate->delete();

        return response()->json([
            'message' => 'Certificado eliminado correctamente.',
        ]);
    }

    public function cattleInfo(Cattle $cattle): JsonResponse
    {
        $cattle->load(['breed', 'ranch', 'currentOwner']);

        return response()->json([
            'cattle' => [
                'id' => $cattle->id,
                'code' => $cattle->code,
                'name' => $cattle->name,
                'ranch_id' => $cattle->ranch_id,
                'owner_id' => $cattle->current_owner_id,
                'purity_percentage' => $cattle->purity_percentage,
                'breed' => $cattle->breed?->name,
                'sex' => $cattle->sex,
                'birth_date' => $cattle->birth_date?->format('Y-m-d'),
            ],
        ]);
    }

    public function downloadPdf(Certificate $certificate): BinaryFileResponse
    {
        if (! $certificate->pdf_path || ! Storage::disk('public')->exists($certificate->pdf_path)) {
            $this->generatePdf($certificate);
            $certificate->refresh();
        }

        abort_unless($certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path), 404);

        return response()->file(Storage::disk('public')->path($certificate->pdf_path));
    }

    public function regeneratePdf(Certificate $certificate): JsonResponse
    {
        $this->generateQr($certificate);
        $this->generatePdf($certificate);

        return response()->json([
            'message' => 'PDF del certificado regenerado correctamente.',
            'pdf_url' => $this->fileUrl($certificate->fresh()->pdf_path),
        ]);
    }

    public function cancel(Certificate $certificate): JsonResponse
    {
        $certificate->update(['status' => 'cancelled']);
        $this->generatePdf($certificate->fresh());

        return response()->json([
            'message' => 'Certificado anulado correctamente.',
        ]);
    }

    public function create() {}

    public function edit(Certificate $certificate) {}

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'ranch_id' => ['nullable', 'exists:ranches,id'],
            'owner_id' => ['nullable', 'exists:owners,id'],
            'veterinarian_id' => ['nullable', 'exists:veterinarians,id'],
            'issue_date' => ['required', 'date'],
            'purity_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'certificate_type' => ['required', Rule::in(array_keys($this->certificateTypes()))],
            'observations' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ], [
            'cattle_id.required' => 'Seleccione el ganado.',
            'cattle_id.exists' => 'El ganado seleccionado no es valido.',
            'issue_date.required' => 'Ingrese la fecha de emision.',
            'issue_date.date' => 'La fecha de emision no es valida.',
            'certificate_type.required' => 'Seleccione el tipo de certificado.',
            'certificate_type.in' => 'El tipo de certificado seleccionado no es valido.',
            'purity_percentage.numeric' => 'La pureza debe ser numerica.',
            'purity_percentage.min' => 'La pureza debe estar entre 0 y 100.',
            'purity_percentage.max' => 'La pureza debe estar entre 0 y 100.',
            'status.required' => 'Seleccione el estado.',
            'status.in' => 'El estado seleccionado no es valido.',
        ]);
    }

    private function generateCertificateNumber(): string
    {
        $year = now()->format('Y');
        $lastNumber = Certificate::withTrashed()
            ->where('certificate_number', 'like', "CERT-{$year}-%")
            ->pluck('certificate_number')
            ->map(fn (?string $number) => preg_match('/CERT-'.$year.'-(\d+)/', (string) $number, $matches) ? (int) $matches[1] : 0)
            ->max();

        return 'CERT-'.$year.'-'.str_pad((string) (((int) $lastNumber) + 1), 6, '0', STR_PAD_LEFT);
    }

    private function generateVerificationCode(): string
    {
        do {
            $code = 'VER-'.Str::upper(Str::random(12));
        } while (Certificate::withTrashed()->where('verification_code', $code)->exists());

        return $code;
    }

    private function generateQr(Certificate $certificate): void
    {
        $url = route('certificates.verify', $certificate->verification_code);
        $writer = extension_loaded('gd') ? new PngWriter() : new SvgWriter();
        $extension = extension_loaded('gd') ? 'png' : 'svg';
        $path = 'certificates/qrs/'.$certificate->verification_code.'.'.$extension;
        $absolutePath = Storage::disk('public')->path($path);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0775, true);
        }

        $result = (new Builder(
            writer: $writer,
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 320,
            margin: 12,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            validateResult: false,
        ))->build();

        $result->saveToFile($absolutePath);
        $certificate->updateQuietly(['qr_code_path' => $path]);
    }

    private function generatePdf(Certificate $certificate): void
    {
        $certificate->loadMissing(['cattle.breed', 'ranch', 'owner', 'veterinarian', 'signatures']);
        $path = 'certificates/pdfs/'.$certificate->certificate_number.'.pdf';
        $absolutePath = Storage::disk('public')->path($path);

        if (! is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0775, true);
        }

        Pdf::loadView('admin.certificates.pdf', [
            'certificate' => $certificate,
            'typeLabel' => $this->typeLabel($certificate->certificate_type),
            'statusLabel' => $this->statusLabel($certificate->status),
            'qrPath' => $certificate->qr_code_path && Storage::disk('public')->exists($certificate->qr_code_path)
                ? Storage::disk('public')->path($certificate->qr_code_path)
                : null,
            'verifyUrl' => route('certificates.verify', $certificate->verification_code),
        ])->setPaper('a4')->save($absolutePath);

        $certificate->updateQuietly(['pdf_path' => $path]);
    }

    private function certificatePayload(Certificate $certificate): array
    {
        return array_merge($certificate->toArray(), [
            'certificate_type_label' => $this->typeLabel($certificate->certificate_type),
            'status_label' => $this->statusLabel($certificate->status),
            'issue_date' => $certificate->issue_date?->format('Y-m-d'),
            'issue_date_formatted' => $certificate->issue_date?->format('d/m/Y'),
            'purity_label' => $certificate->purity_percentage !== null ? number_format((float) $certificate->purity_percentage, 2).'%' : '-',
            'cattle_label' => $this->cattleLabel($certificate->cattle),
            'cattle_code' => $certificate->cattle?->code,
            'cattle_name' => $certificate->cattle?->name,
            'cattle_breed_name' => $certificate->cattle?->breed?->name,
            'cattle_sex_label' => $this->sexLabel($certificate->cattle?->sex),
            'cattle_birth_date' => $certificate->cattle?->birth_date?->format('d/m/Y'),
            'cattle_purity_label' => $certificate->cattle?->purity_percentage !== null ? number_format((float) $certificate->cattle->purity_percentage, 2).'%' : '-',
            'cattle_photo_url' => $this->fileUrl($certificate->cattle?->main_photo_path),
            'ranch_name' => $this->ranchDisplayName($certificate->ranch),
            'ranch_document' => $certificate->ranch?->document_number,
            'ranch_address' => $certificate->ranch?->address,
            'owner_name' => $this->ownerDisplayName($certificate->owner),
            'owner_document' => $certificate->owner?->document_number,
            'owner_phone' => $certificate->owner?->phone,
            'owner_email' => $certificate->owner?->email,
            'veterinarian_name' => $certificate->veterinarian?->full_name,
            'veterinarian_license' => $certificate->veterinarian?->license_number,
            'veterinarian_specialty' => $certificate->veterinarian?->specialty,
            'qr_url' => $this->fileUrl($certificate->qr_code_path),
            'pdf_url' => $this->fileUrl($certificate->pdf_path),
            'verify_url' => route('certificates.verify', $certificate->verification_code),
            'signatures' => $certificate->signatures->map(fn (CertificateSignature $signature) => [
                'id' => $signature->id,
                'person_type' => $signature->person_type,
                'person_type_label' => $this->personTypeLabel($signature->person_type),
                'person_name' => $signature->person_name,
                'position' => $signature->position,
                'signature_url' => $this->fileUrl($signature->signature_path),
                'seal_url' => $this->fileUrl($signature->seal_path),
            ])->values(),
            'created_at_formatted' => $certificate->created_at?->format('d/m/Y H:i'),
            'updated_at_formatted' => $certificate->updated_at?->format('d/m/Y H:i'),
        ]);
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

    private function statuses(): array
    {
        return [
            'issued' => 'Emitido',
            'cancelled' => 'Anulado',
            'expired' => 'Vencido',
        ];
    }

    private function typeLabel(?string $type): string
    {
        return $this->certificateTypes()[$type] ?? '-';
    }

    private function statusLabel(?string $status): string
    {
        return $this->statuses()[$status] ?? '-';
    }

    private function typeBadge(?string $type): string
    {
        $classes = [
            'breed' => 'badge-success',
            'genealogy' => 'badge-primary',
            'ownership' => 'badge-warning',
            'purity' => 'badge-info',
        ];

        return '<span class="badge '.($classes[$type] ?? 'badge-secondary').'">'.$this->typeLabel($type).'</span>';
    }

    private function statusBadge(?string $status): string
    {
        $classes = [
            'issued' => 'badge-success',
            'cancelled' => 'badge-danger',
            'expired' => 'badge-secondary',
        ];

        return '<span class="badge '.($classes[$status] ?? 'badge-secondary').'">'.$this->statusLabel($status).'</span>';
    }

    private function cattleLabel(?Cattle $cattle): string
    {
        return $cattle ? trim($cattle->code.' - '.($cattle->name ?: 'Sin nombre')) : '-';
    }

    private function ranchDisplayName(?Ranch $ranch): ?string
    {
        return $ranch?->business_name ?: $ranch?->name;
    }

    private function ownerDisplayName(?Owner $owner): ?string
    {
        return $owner?->owner_type === 'company' && $owner->business_name ? $owner->business_name : $owner?->full_name;
    }

    private function sexLabel(?string $sex): string
    {
        return match ($sex) {
            'male' => 'Macho',
            'female' => 'Hembra',
            default => '-',
        };
    }

    private function personTypeLabel(?string $type): string
    {
        return match ($type) {
            'owner' => 'Dueno',
            'veterinarian' => 'Veterinario',
            'representative' => 'Representante',
            'certifier' => 'Certificador',
            'other' => 'Otro',
            default => '-',
        };
    }

    private function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
