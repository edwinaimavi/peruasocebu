<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->filled('website')) {
            return $this->successResponse($request);
        }

        $request->merge([
            'full_name' => $this->cleanText($request->input('full_name')),
            'phone' => $this->cleanText($request->input('phone')),
            'email' => $this->cleanText($request->input('email')),
            'subject' => $this->cleanText($request->input('subject')),
            'message' => $this->cleanText($request->input('message')),
        ]);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'full_name.required' => 'Ingrese su nombre completo.',
            'message.required' => 'Ingrese su mensaje.',
            'email.email' => 'Ingrese un correo valido.',
        ]);

        ContactMessage::create([
            'full_name' => $data['full_name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'status' => 'new',
        ]);

        return $this->successResponse($request);
    }

    private function successResponse(Request $request): JsonResponse|RedirectResponse
    {
        $message = 'Gracias por contactarnos. Hemos recibido tu mensaje y nos comunicaremos contigo pronto.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    private function cleanText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(strip_tags($value));

        return $value === '' ? null : Str::squish($value);
    }
}
