<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\CattlePhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CattlePhotoController extends Controller
{
    public function listByCattle(Cattle $cattle): JsonResponse
    {
        $this->ensureLegacyMainPhotoExists($cattle);

        return response()->json([
            'photos' => $cattle->photos()->get()->map(fn (CattlePhoto $photo) => $this->photoPayload($photo))->values(),
            'main_photo_path' => $cattle->fresh()->main_photo_path,
            'main_photo_url' => $this->photoUrl($cattle->fresh()->main_photo_path),
        ]);
    }

    public function store(Request $request, Cattle $cattle): JsonResponse
    {
        $data = $this->validatedData($request, true);
        $path = $request->file('image')->store('cattle/photos', 'public');

        DB::transaction(function () use ($cattle, $data, $path): void {
            $isMain = $this->shouldBeMain($cattle, $data['is_main'] ?? false);

            $photo = CattlePhoto::create([
                'cattle_id' => $cattle->id,
                'image_path' => $path,
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'is_main' => $isMain,
                'sort_order' => $data['sort_order'] ?? $this->nextSortOrder($cattle),
            ]);

            if ($isMain) {
                $this->setMainPhoto($photo);
            }
        });

        return response()->json(['message' => 'Foto agregada correctamente.']);
    }

    public function show(CattlePhoto $photo): JsonResponse
    {
        return response()->json(['photo' => $this->photoPayload($photo)]);
    }

    public function update(Request $request, CattlePhoto $photo): JsonResponse
    {
        $data = $this->validatedData($request, false);
        $oldPath = null;

        DB::transaction(function () use ($request, $photo, $data, &$oldPath): void {
            $payload = [
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
            ];

            if ($request->hasFile('image')) {
                $oldPath = $photo->image_path;
                $payload['image_path'] = $request->file('image')->store('cattle/photos', 'public');
            }

            $photo->update($payload);

            if ($request->boolean('is_main')) {
                $this->setMainPhoto($photo->refresh());
            } elseif ($photo->is_main) {
                $photo->cattle->update(['main_photo_path' => $photo->image_path]);
            }
        });

        $this->deletePhotoFile($oldPath);

        return response()->json(['message' => 'Foto actualizada correctamente.']);
    }

    public function destroy(CattlePhoto $photo): JsonResponse
    {
        $cattle = $photo->cattle;
        $path = $photo->image_path;
        $wasMain = $photo->is_main;

        DB::transaction(function () use ($photo, $cattle, $wasMain): void {
            $photo->delete();

            if (! $wasMain) {
                return;
            }

            $replacement = $cattle->photos()->first();

            if ($replacement) {
                $this->setMainPhoto($replacement);
                return;
            }

            $cattle->update(['main_photo_path' => null]);
        });

        $this->deletePhotoFile($path);

        return response()->json(['message' => 'Foto eliminada correctamente.']);
    }

    public function setMain(CattlePhoto $photo): JsonResponse
    {
        $this->setMainPhoto($photo);

        return response()->json(['message' => 'Foto principal actualizada correctamente.']);
    }

    private function validatedData(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_main' => ['nullable', 'boolean'],
        ], [
            'image.required' => 'Seleccione una imagen.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser JPG, PNG o WEBP.',
            'image.max' => 'La imagen no debe superar los 4 MB.',
        ]);
    }

    private function setMainPhoto(CattlePhoto $photo): void
    {
        CattlePhoto::where('cattle_id', $photo->cattle_id)
            ->whereKeyNot($photo->id)
            ->update(['is_main' => false]);

        $photo->update(['is_main' => true]);
        $photo->cattle->update(['main_photo_path' => $photo->image_path]);
    }

    private function shouldBeMain(Cattle $cattle, bool $requested): bool
    {
        return $requested || ! $cattle->photos()->exists() || blank($cattle->main_photo_path);
    }

    private function nextSortOrder(Cattle $cattle): int
    {
        return ((int) $cattle->photos()->max('sort_order')) + 1;
    }

    private function ensureLegacyMainPhotoExists(Cattle $cattle): void
    {
        if (! $cattle->main_photo_path || $cattle->photos()->exists()) {
            return;
        }

        CattlePhoto::create([
            'cattle_id' => $cattle->id,
            'image_path' => $cattle->main_photo_path,
            'title' => 'Foto principal',
            'is_main' => true,
            'sort_order' => 0,
        ]);
    }

    private function photoPayload(CattlePhoto $photo): array
    {
        return array_merge($photo->toArray(), [
            'image_url' => $this->photoUrl($photo->image_path),
        ]);
    }

    private function photoUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    private function deletePhotoFile(?string $path): void
    {
        if ($path && ! CattlePhoto::where('image_path', $path)->exists()) {
            Storage::disk('public')->delete($path);
        }
    }
}
