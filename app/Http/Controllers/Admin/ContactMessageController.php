<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.contact-messages.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.contact-messages.update')->only('markRead', 'markAnswered', 'markNew');
        $this->middleware('can:admin.contact-messages.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.contact_messages.index', [
            'statuses' => $this->statuses(),
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', Rule::in(array_keys($this->statuses()))],
        ]);

        $messages = ContactMessage::query()
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest('id');

        return DataTables::eloquent($messages)
            ->addIndexColumn()
            ->setRowClass(fn (ContactMessage $message) => $message->status === 'new' ? 'contact-message-new' : '')
            ->editColumn('full_name', fn (ContactMessage $message) => '<strong>'.e($message->full_name).'</strong>')
            ->editColumn('phone', fn (ContactMessage $message) => $message->phone ?: '-')
            ->editColumn('email', fn (ContactMessage $message) => $message->email ?: '-')
            ->editColumn('subject', fn (ContactMessage $message) => $message->subject ?: 'Sin asunto')
            ->editColumn('status', fn (ContactMessage $message) => $this->statusBadge($message->status))
            ->editColumn('created_at', fn (ContactMessage $message) => $message->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (ContactMessage $message) => view(
                'admin.contact_messages.partials.acciones',
                compact('message')
            )->render())
            ->rawColumns(['full_name', 'status', 'acciones'])
            ->toJson();
    }

    public function show(ContactMessage $contactMessage): JsonResponse
    {
        if ($contactMessage->status === 'new') {
            $contactMessage->update(['status' => 'read']);
        }

        return response()->json([
            'message' => $this->messagePayload($contactMessage->fresh()),
        ]);
    }

    public function markRead(ContactMessage $contactMessage): JsonResponse
    {
        return $this->changeStatus($contactMessage, 'read', 'Mensaje marcado como leido correctamente.');
    }

    public function markAnswered(ContactMessage $contactMessage): JsonResponse
    {
        return $this->changeStatus($contactMessage, 'answered', 'Mensaje marcado como respondido correctamente.');
    }

    public function markNew(ContactMessage $contactMessage): JsonResponse
    {
        return $this->changeStatus($contactMessage, 'new', 'Mensaje marcado como nuevo correctamente.');
    }

    public function destroy(ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->delete();

        return response()->json([
            'message' => 'Mensaje eliminado correctamente.',
        ]);
    }

    private function changeStatus(ContactMessage $contactMessage, string $status, string $message): JsonResponse
    {
        $contactMessage->update(['status' => $status]);

        return response()->json([
            'message' => $message,
            'contactMessage' => $this->messagePayload($contactMessage->fresh()),
        ]);
    }

    private function messagePayload(ContactMessage $message): array
    {
        return array_merge($message->toArray(), [
            'status_label' => $this->statusLabel($message->status),
            'status_badge' => $this->statusBadge($message->status),
            'phone_label' => $message->phone ?: '-',
            'email_label' => $message->email ?: '-',
            'subject_label' => $message->subject ?: 'Sin asunto',
            'whatsapp_url' => $this->whatsAppUrl($message->phone),
            'mailto_url' => $message->email ? 'mailto:'.$message->email : null,
            'created_at_formatted' => $message->created_at?->format('d/m/Y H:i'),
            'updated_at_formatted' => $message->updated_at?->format('d/m/Y H:i'),
        ]);
    }

    private function whatsAppUrl(?string $phone): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if ($number === '') {
            return null;
        }

        if (! str_starts_with($number, '51')) {
            $number = '51'.$number;
        }

        return 'https://wa.me/'.$number;
    }

    private function statuses(): array
    {
        return [
            'new' => 'Nuevo',
            'read' => 'Leido',
            'answered' => 'Respondido',
        ];
    }

    private function statusLabel(?string $status): string
    {
        return $this->statuses()[$status] ?? '-';
    }

    private function statusBadge(?string $status): string
    {
        $classes = [
            'new' => 'badge-primary',
            'read' => 'badge-secondary',
            'answered' => 'badge-success',
        ];

        return '<span class="badge '.($classes[$status] ?? 'badge-light').' px-2 py-1">'.$this->statusLabel($status).'</span>';
    }
}
