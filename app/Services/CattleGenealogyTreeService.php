<?php

namespace App\Services;

use App\Models\Cattle;
use App\Models\CattleGenealogyLink;
use Illuminate\Support\Facades\Storage;

class CattleGenealogyTreeService
{
    public function build(Cattle $cattle): array
    {
        $cattle->loadMissing([
            'father.breed',
            'father.father.breed',
            'father.mother.breed',
            'father.genealogyLinks.breed',
            'father.genealogyLinks.relativeCattle.breed',
            'mother.breed',
            'mother.father.breed',
            'mother.mother.breed',
            'mother.genealogyLinks.breed',
            'mother.genealogyLinks.relativeCattle.breed',
            'genealogyLinks.breed',
            'genealogyLinks.relativeCattle.breed',
        ]);

        $tree = collect();

        $cattle->genealogyLinks->each(function (CattleGenealogyLink $link) use ($tree): void {
            $path = $link->lineage_path ?: $this->legacyRelationToPath($link->relation_type);

            if (! $path) {
                return;
            }

            $path = strtoupper($path);
            $tree->put($path, $this->linkNode($link, $path));
        });

        if ($cattle->father && ! $tree->has('F')) {
            $tree->put('F', $this->cattleNode($cattle->father, 'F', 'Padre'));
        }

        if ($cattle->mother && ! $tree->has('M')) {
            $tree->put('M', $this->cattleNode($cattle->mother, 'M', 'Madre'));
        }

        $this->addResolvedNode($tree, 'FF', 'Abuelo paterno', $cattle->father?->father);
        $this->addResolvedNode($tree, 'FM', 'Abuela paterna', $cattle->father?->mother);
        $this->addResolvedNode($tree, 'MF', 'Abuelo materno', $cattle->mother?->father);
        $this->addResolvedNode($tree, 'MM', 'Abuela materna', $cattle->mother?->mother);
        $this->addParentGenealogyLinks($tree, $cattle->father, 'F');
        $this->addParentGenealogyLinks($tree, $cattle->mother, 'M');

        return $tree
            ->sortBy([
                ['level', 'asc'],
                ['path', 'asc'],
            ])
            ->values()
            ->all();
    }

    private function addResolvedNode($tree, string $path, string $label, ?Cattle $relative): void
    {
        if (! $relative || $tree->has($path)) {
            return;
        }

        $tree->put($path, array_merge($this->cattleNode($relative, $path, $label), [
            'source' => 'resolved_from_parent',
        ]));
    }

    private function addParentGenealogyLinks($tree, ?Cattle $parent, string $prefix): void
    {
        if (! $parent) {
            return;
        }

        $parent->genealogyLinks
            ->filter(function (CattleGenealogyLink $link) {
                return in_array($link->lineage_path ?: $this->legacyRelationToPath($link->relation_type), ['F', 'M'], true);
            })
            ->each(function (CattleGenealogyLink $link) use ($tree, $prefix): void {
                $parentPath = $link->lineage_path ?: $this->legacyRelationToPath($link->relation_type);
                $path = $prefix.$parentPath;

                if ($tree->has($path)) {
                    return;
                }

                $tree->put($path, array_merge($this->linkNode($link, $path), [
                    'source' => 'resolved_from_parent_genealogy_link',
                ]));
            });
    }

    private function linkNode(CattleGenealogyLink $link, string $path): array
    {
        return [
            'id' => $link->id,
            'path' => $path,
            'level' => strlen($path),
            'label' => $this->lineagePathLabel($path),
            'code' => $link->relativeCattle?->code ?? $link->relative_code,
            'name' => $link->relativeCattle?->name ?? $link->relative_name,
            'breed' => $link->relativeCattle?->breed?->name ?? $link->breed?->name,
            'photo_url' => $link->relativeCattle ? $this->photoUrl($link->relativeCattle->main_photo_path) : null,
            'is_registered' => (bool) $link->relative_cattle_id,
        ];
    }

    private function cattleNode(Cattle $relative, string $path, string $label): array
    {
        return [
            'id' => null,
            'path' => $path,
            'level' => strlen($path),
            'label' => $label,
            'code' => $relative->code,
            'name' => $relative->name,
            'breed' => $relative->breed?->name,
            'photo_url' => $this->photoUrl($relative->main_photo_path),
            'is_registered' => true,
        ];
    }

    private function legacyRelationToPath(?string $relationType): ?string
    {
        return match ($relationType) {
            'father' => 'F',
            'mother' => 'M',
            'paternal_grandfather' => 'FF',
            'paternal_grandmother' => 'FM',
            'maternal_grandfather' => 'MF',
            'maternal_grandmother' => 'MM',
            default => null,
        };
    }

    private function lineagePathLabel(?string $path): string
    {
        return match ($path) {
            'F' => 'Padre',
            'M' => 'Madre',
            'FF' => 'Abuelo paterno',
            'FM' => 'Abuela paterna',
            'MF' => 'Abuelo materno',
            'MM' => 'Abuela materna',
            default => $path ? 'Generacion '.strlen($path).' - Linea '.$path : 'Familiar',
        };
    }

    private function photoUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
