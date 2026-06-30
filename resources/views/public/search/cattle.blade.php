@extends('public.layouts.app')

@section('title', ($cattle->name ?: $cattle->code).' | Consulta publica PERU ASOCEBU')
@section('body_class', 'public-cattle-page')
@section('main_class', 'public-cattle-main')

@section('content')
    @php
        $mainPhoto = $cattle->main_photo_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($cattle->main_photo_path)
            : ($cattle->photos->first()?->image_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($cattle->photos->first()->image_path) : null);
        $ownerName = $cattle->currentOwner?->owner_type === 'company' && $cattle->currentOwner?->business_name
            ? $cattle->currentOwner->business_name
            : $cattle->currentOwner?->full_name;
        $ranchName = $cattle->ranch?->business_name ?: $cattle->ranch?->name;
        $sexLabel = match ($cattle->sex) {
            'male' => 'Macho',
            'female' => 'Hembra',
            default => 'No registrado',
        };
        $statusLabel = match ($cattle->status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'sold' => 'Vendido',
            'deceased' => 'Fallecido',
            default => $cattle->status ?: 'No registrado',
        };
        $tree = collect($genealogyTree ?? [])->keyBy('path');
        $father = $tree->get('F');
        $mother = $tree->get('M');
        $grandparents = [
            ['key' => 'FF', 'label' => 'Abuelo paterno'],
            ['key' => 'FM', 'label' => 'Abuela paterna'],
            ['key' => 'MF', 'label' => 'Abuelo materno'],
            ['key' => 'MM', 'label' => 'Abuela materna'],
        ];
        $extendedFamily = collect($genealogyTree ?? [])->filter(fn ($item) => strlen($item['path'] ?? '') > 2);
        $firstCertificate = $cattle->certificates->first();
    @endphp

    <section class="public-cattle-container">
        <div class="public-cattle-actions-top">
            <a href="{{ route('public.home') }}" class="public-cattle-link">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                Volver al inicio
            </a>
            <a href="{{ route('public.home') }}#consulta" class="public-cattle-link public-cattle-link-light">
                Nueva consulta
            </a>
        </div>

        <header class="public-cattle-hero">
            <div>
                <span class="public-cattle-eyebrow">PERU ASOCEBU</span>
                <h1>Consulta publica de ganado</h1>
                <p>Registro institucional encontrado para el codigo <strong>{{ $query }}</strong>.</p>
            </div>
            <div class="public-cattle-hero-code">
                <span>Codigo de animal</span>
                <strong>{{ $cattle->code }}</strong>
            </div>
        </header>

        <section class="public-cattle-profile-card">
            <div class="public-cattle-photo">
                @if ($mainPhoto)
                    <img src="{{ $mainPhoto }}" alt="Foto de {{ $cattle->name ?: $cattle->code }}">
                @else
                    <div class="public-cattle-photo-placeholder">
                        <i class="fas fa-cow" aria-hidden="true"></i>
                    </div>
                @endif
            </div>

            <div class="public-cattle-summary">
                <span class="public-cattle-badge">{{ $statusLabel }}</span>
                <h2>{{ $cattle->name ?: 'Ganado registrado' }}</h2>
                <p>{{ $cattle->breed?->name ?: 'Raza no registrada' }}</p>

                <div class="public-cattle-data-grid">
                    <article><span>Codigo</span><strong>{{ $cattle->code }}</strong></article>
                    <article><span>Sexo</span><strong>{{ $sexLabel }}</strong></article>
                    <article><span>Nacimiento</span><strong>{{ $cattle->birth_date?->format('d/m/Y') ?: 'No registrado' }}</strong></article>
                    <article><span>Edad aproximada</span><strong>{{ $cattle->birth_date ? $cattle->birth_date->diffForHumans(null, true, false, 2) : 'No registrada' }}</strong></article>
                    <article><span>Pureza racial</span><strong>{{ $cattle->purity_percentage !== null ? number_format((float) $cattle->purity_percentage, 2).'%' : 'No registrada' }}</strong></article>
                    <article><span>Criadero / Hacienda</span><strong>{{ $ranchName ?: 'No registrado' }}</strong></article>
                    <article><span>Propietario actual</span><strong>{{ $ownerName ?: 'No registrado' }}</strong></article>
                    <article><span>Color</span><strong>{{ $cattle->color ?: 'No registrado' }}</strong></article>
                </div>
            </div>
        </section>

        <section class="public-cattle-section public-pedigree-section">
            <div class="public-section-heading">
                <span>Arbol genealogico bovino</span>
                <h2>Linaje registrado</h2>
            </div>

            <div class="public-pedigree-board public-pedigree-tree js-pedigree-tree">
                <svg class="pedigree-lines" aria-hidden="true"></svg>
                <div class="public-pedigree-generation public-pedigree-grandparents">
                    @foreach ($grandparents as $grandparent)
                        @include('public.search.partials.pedigree-node', [
                            'item' => $tree->get($grandparent['key']),
                            'label' => $grandparent['label'],
                            'path' => $grandparent['key'],
                            'empty' => ! $tree->has($grandparent['key']),
                            'compact' => true,
                        ])
                    @endforeach
                </div>

                <div class="public-pedigree-generation public-pedigree-parents">
                    @include('public.search.partials.pedigree-node', [
                        'item' => $father,
                        'label' => 'Padre',
                        'path' => 'F',
                        'empty' => ! $father,
                    ])
                    @include('public.search.partials.pedigree-node', [
                        'item' => $mother,
                        'label' => 'Madre',
                        'path' => 'M',
                        'empty' => ! $mother,
                    ])
                </div>

                <div class="public-pedigree-current">
                    <div class="public-pedigree-node is-current current-animal" data-path="CURRENT">
                        <div class="public-pedigree-avatar">
                            @if ($mainPhoto)
                                <img src="{{ $mainPhoto }}" alt="{{ $cattle->name ?: $cattle->code }}">
                            @else
                                <i class="fas fa-cow" aria-hidden="true"></i>
                            @endif
                        </div>
                        <div class="public-pedigree-info">
                            <small>Animal consultado</small>
                            <strong>{{ trim($cattle->code.' - '.($cattle->name ?: 'Sin nombre')) }}</strong>
                            <span>Raza: {{ $cattle->breed?->name ?: 'No registrada' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($extendedFamily->isNotEmpty())
                <div class="public-pedigree-extra">
                    <h3>Ancestros adicionales</h3>
                    <div class="public-pedigree-extra-grid">
                        @foreach ($extendedFamily as $relative)
                            @include('public.search.partials.pedigree-node', [
                                'item' => $relative,
                                'label' => $relative['label'] ?? 'Familiar',
                                'path' => $relative['path'] ?? '',
                                'empty' => false,
                                'compact' => true,
                            ])
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        <section class="public-cattle-grid-two">
            <article class="public-cattle-section">
                <div class="public-section-heading">
                    <span>Certificacion</span>
                    <h2>Certificados asociados</h2>
                </div>

                <div class="public-certificate-list">
                    @forelse ($cattle->certificates as $certificate)
                        @php
                            $certificateType = match ($certificate->certificate_type) {
                                'registration' => 'Registro',
                                'ownership' => 'Propiedad',
                                'genealogy' => 'Genealogia',
                                'health' => 'Sanitario',
                                default => $certificate->certificate_type ?: 'No registrado',
                            };
                            $certificateStatus = match ($certificate->status) {
                                'issued' => 'Emitido',
                                'cancelled' => 'Anulado',
                                'expired' => 'Vencido',
                                default => $certificate->status ?: 'No registrado',
                            };
                            $pdfUrl = $certificate->pdf_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($certificate->pdf_path) : null;
                        @endphp
                        <div class="public-certificate-card">
                            <div>
                                <span>{{ $certificateType }}</span>
                                <strong>{{ $certificate->certificate_number ?: 'Sin numero' }}</strong>
                                <small>Estado: {{ $certificateStatus }}</small>
                            </div>
                            <div class="public-certificate-actions">
                                @if ($certificate->verification_code)
                                    <a href="{{ route('certificates.verify', $certificate->verification_code) }}">Verificar</a>
                                @endif
                                @if ($pdfUrl)
                                    <a href="{{ $pdfUrl }}" target="_blank" rel="noopener">PDF</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="public-empty-text">No hay certificados publicos asociados a este animal.</p>
                    @endforelse
                </div>
            </article>

            <article class="public-cattle-section">
                <div class="public-section-heading">
                    <span>Galeria</span>
                    <h2>Fotografias</h2>
                </div>

                @if ($cattle->photos->isNotEmpty())
                    <div class="public-cattle-gallery">
                        @foreach ($cattle->photos as $photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($photo->image_path) }}" alt="{{ $photo->title ?: 'Foto de ganado' }}">
                        @endforeach
                    </div>
                @else
                    <p class="public-empty-text">No hay fotografias publicas registradas.</p>
                @endif
            </article>
        </section>

        <div class="public-cattle-bottom-actions">
            <a href="{{ route('public.home') }}" class="public-cattle-btn public-cattle-btn-light">Volver al inicio</a>
            <a href="{{ route('public.home') }}#consulta" class="public-cattle-btn">Nueva consulta</a>
            @if ($firstCertificate?->verification_code)
                <a href="{{ route('certificates.verify', $firstCertificate->verification_code) }}" class="public-cattle-btn public-cattle-btn-gold">Ver certificado</a>
            @endif
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .public-cattle-page {
            background: #f7f3ea;
            color: #123524;
        }

        .public-cattle-main {
            padding: 0;
        }

        .public-cattle-container {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 32px 0 72px;
        }

        .public-cattle-actions-top,
        .public-cattle-bottom-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .public-cattle-link,
        .public-cattle-btn,
        .public-certificate-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 10px 18px;
            border-radius: 999px;
            border: 1px solid rgba(18, 53, 36, 0.18);
            color: #123524;
            background: #fff;
            font-weight: 800;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .public-cattle-link:hover,
        .public-cattle-btn:hover,
        .public-certificate-actions a:hover {
            color: #123524;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 16px 34px rgba(18, 53, 36, 0.14);
        }

        .public-cattle-link-light,
        .public-cattle-btn-light {
            background: rgba(255, 255, 255, 0.68);
        }

        .public-cattle-btn-gold {
            background: #d8be78;
            border-color: #b88a2a;
        }

        .public-cattle-hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 24px;
            align-items: center;
            padding: 38px;
            border-radius: 24px;
            background: linear-gradient(135deg, #123524 0%, #1c4b34 100%);
            color: #fff;
            box-shadow: 0 24px 60px rgba(18, 53, 36, 0.22);
        }

        .public-cattle-eyebrow,
        .public-section-heading span,
        .public-cattle-hero-code span {
            display: block;
            color: #d8be78;
            font-size: 0.76rem;
            font-weight: 900;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .public-cattle-hero h1 {
            margin: 8px 0 10px;
            font-size: clamp(2rem, 4vw, 3.8rem);
            line-height: 1.02;
            font-weight: 900;
            letter-spacing: 0;
        }

        .public-cattle-hero p {
            max-width: 640px;
            margin: 0;
            color: rgba(255, 255, 255, 0.82);
            font-size: 1.05rem;
        }

        .public-cattle-hero-code {
            min-width: 220px;
            padding: 20px;
            border: 1px solid rgba(216, 190, 120, 0.34);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.08);
        }

        .public-cattle-hero-code strong {
            display: block;
            margin-top: 8px;
            color: #fff;
            font-size: 1.35rem;
        }

        .public-cattle-profile-card,
        .public-cattle-section {
            margin-top: 22px;
            border: 1px solid rgba(18, 53, 36, 0.1);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 22px 54px rgba(18, 53, 36, 0.1);
        }

        .public-cattle-profile-card {
            display: grid;
            grid-template-columns: minmax(280px, 430px) minmax(0, 1fr);
            gap: 28px;
            padding: 22px;
        }

        .public-cattle-photo {
            min-height: 390px;
            overflow: hidden;
            border-radius: 18px;
            background: #e8dfc9;
        }

        .public-cattle-photo img,
        .public-cattle-gallery img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .public-cattle-photo-placeholder {
            display: grid;
            height: 100%;
            min-height: 390px;
            place-items: center;
            color: #b88a2a;
            font-size: 4.2rem;
        }

        .public-cattle-summary {
            padding: 8px 8px 8px 0;
        }

        .public-cattle-badge {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(216, 190, 120, 0.24);
            color: #7c5f19;
            font-weight: 900;
        }

        .public-cattle-summary h2 {
            margin: 14px 0 6px;
            color: #123524;
            font-size: clamp(1.85rem, 3vw, 3rem);
            line-height: 1.06;
            font-weight: 900;
        }

        .public-cattle-summary p {
            margin: 0 0 22px;
            color: #58685f;
            font-size: 1.02rem;
            font-weight: 700;
        }

        .public-cattle-data-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .public-cattle-data-grid article,
        .public-certificate-card {
            min-width: 0;
            padding: 15px;
            border: 1px solid rgba(18, 53, 36, 0.1);
            border-radius: 16px;
            background: #fbfaf5;
        }

        .public-cattle-data-grid span,
        .public-certificate-card span {
            display: block;
            color: #8b7a4e;
            font-size: 0.78rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .public-cattle-data-grid strong,
        .public-certificate-card strong {
            display: block;
            margin-top: 5px;
            color: #123524;
            font-size: 0.98rem;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        .public-cattle-section {
            padding: 26px;
        }

        .public-section-heading {
            margin-bottom: 20px;
        }

        .public-section-heading h2 {
            margin: 4px 0 0;
            color: #123524;
            font-size: 1.55rem;
            font-weight: 900;
        }

        .public-pedigree-board {
            position: relative;
            display: grid;
            gap: 18px;
            padding: 18px;
            border-radius: 18px;
            background: #f7f3ea;
            overflow-x: auto;
        }

        .pedigree-lines {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: visible;
            pointer-events: none;
            z-index: 1;
        }

        .pedigree-line-path {
            fill: none;
            stroke: rgba(18, 53, 36, 0.32);
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .pedigree-line-path.strong {
            stroke: rgba(200, 155, 60, 0.55);
        }

        .public-pedigree-generation {
            display: grid;
            gap: 14px;
        }

        .public-pedigree-grandparents {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .public-pedigree-parents {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: min(760px, 100%);
            margin: 0 auto;
        }

        .public-pedigree-current {
            width: min(390px, 100%);
            margin: 0 auto;
        }

        .public-pedigree-node {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            gap: 12px;
            align-items: center;
            min-height: 92px;
            padding: 12px;
            border: 1px solid rgba(18, 53, 36, 0.12);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 12px 28px rgba(18, 53, 36, 0.08);
            position: relative;
            z-index: 2;
        }

        .public-pedigree-node.is-current {
            border-color: rgba(184, 138, 42, 0.55);
            background: #fff9e8;
            box-shadow: 0 18px 38px rgba(184, 138, 42, 0.16);
        }

        .public-pedigree-node.is-empty {
            background: rgba(255, 255, 255, 0.62);
            border-style: dashed;
            box-shadow: none;
        }

        .public-pedigree-node.is-compact {
            grid-template-columns: 48px minmax(0, 1fr);
            min-height: 82px;
            padding: 10px;
        }

        .public-pedigree-avatar {
            width: 58px;
            height: 58px;
            overflow: hidden;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: rgba(216, 190, 120, 0.22);
            color: #b88a2a;
            font-size: 1.35rem;
        }

        .public-pedigree-node.is-compact .public-pedigree-avatar {
            width: 48px;
            height: 48px;
        }

        .public-pedigree-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .public-pedigree-info {
            min-width: 0;
        }

        .public-pedigree-info small {
            display: block;
            color: #8b7a4e;
            font-size: 0.73rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .public-pedigree-info strong {
            display: block;
            margin: 3px 0;
            color: #123524;
            font-size: 0.94rem;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .public-pedigree-info span {
            display: block;
            color: #627168;
            font-size: 0.82rem;
            line-height: 1.25;
        }

        .public-pedigree-extra {
            margin-top: 22px;
        }

        .public-pedigree-extra h3 {
            margin: 0 0 12px;
            color: #123524;
            font-size: 1rem;
            font-weight: 900;
        }

        .public-pedigree-extra-grid,
        .public-cattle-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
        }

        .public-cattle-grid-two {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 0.85fr);
            gap: 22px;
        }

        .public-certificate-list {
            display: grid;
            gap: 12px;
        }

        .public-certificate-card {
            display: flex;
            gap: 14px;
            align-items: center;
            justify-content: space-between;
            background: #fbfaf5;
        }

        .public-certificate-card small {
            display: block;
            margin-top: 5px;
            color: #647269;
            font-weight: 700;
        }

        .public-certificate-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .public-certificate-actions a {
            min-height: 36px;
            padding: 8px 14px;
            background: #123524;
            border-color: #123524;
            color: #fff;
            font-size: 0.86rem;
        }

        .public-certificate-actions a:hover {
            color: #fff;
            background: #1c4b34;
        }

        .public-cattle-gallery img {
            aspect-ratio: 4 / 3;
            border-radius: 14px;
            border: 1px solid rgba(18, 53, 36, 0.1);
        }

        .public-empty-text {
            margin: 0;
            padding: 18px;
            border-radius: 14px;
            background: #fbfaf5;
            color: #647269;
            font-weight: 700;
        }

        .public-cattle-bottom-actions {
            justify-content: center;
            margin: 26px 0 0;
        }

        @media (max-width: 992px) {
            .public-cattle-hero,
            .public-cattle-profile-card,
            .public-cattle-grid-two {
                grid-template-columns: 1fr;
            }

            .public-cattle-hero-code {
                min-width: 0;
            }

            .public-cattle-photo,
            .public-cattle-photo-placeholder {
                min-height: 320px;
            }

            .public-pedigree-grandparents {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .public-cattle-container {
                width: min(100% - 22px, 1180px);
                padding: 18px 0 48px;
            }

            .public-cattle-hero,
            .public-cattle-section {
                border-radius: 18px;
                padding: 22px;
            }

            .public-cattle-profile-card {
                padding: 14px;
                border-radius: 18px;
            }

            .public-cattle-data-grid,
            .public-pedigree-grandparents,
            .public-pedigree-parents {
                grid-template-columns: 1fr;
            }

            .public-cattle-actions-top {
                justify-content: center;
            }

            .public-cattle-link,
            .public-cattle-btn {
                width: 100%;
            }

            .public-certificate-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .public-certificate-actions {
                width: 100%;
                justify-content: stretch;
            }

            .public-certificate-actions a {
                flex: 1 1 120px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function drawPedigreeLines(treeElement) {
            const tree = typeof jQuery !== 'undefined' && treeElement instanceof jQuery ? treeElement[0] : treeElement;

            if (!tree) {
                return;
            }

            const svg = tree.querySelector('.pedigree-lines');

            if (!svg) {
                return;
            }

            svg.innerHTML = '';

            const width = Math.max(tree.scrollWidth, tree.clientWidth);
            const height = Math.max(tree.scrollHeight, tree.clientHeight);

            if (!width || !height) {
                return;
            }

            const treeRect = tree.getBoundingClientRect();

            svg.setAttribute('width', width);
            svg.setAttribute('height', height);
            svg.setAttribute('viewBox', `0 0 ${width} ${height}`);

            function getNode(path) {
                return tree.querySelector(`[data-path="${path}"]`);
            }

            function getPoint(node, position) {
                if (!node) {
                    return null;
                }

                const rect = node.getBoundingClientRect();
                const x = rect.left - treeRect.left + tree.scrollLeft + (rect.width / 2);
                const y = position === 'top'
                    ? rect.top - treeRect.top + tree.scrollTop
                    : rect.bottom - treeRect.top + tree.scrollTop;

                return { x, y };
            }

            function isEmptyNode(node) {
                return node.classList.contains('is-empty') || node.classList.contains('pedigree-node-empty');
            }

            function connect(fromPath, toPath, cssClass = '') {
                const fromNode = getNode(fromPath);
                const toNode = getNode(toPath);

                if (!fromNode || !toNode || isEmptyNode(fromNode) || isEmptyNode(toNode)) {
                    return;
                }

                const start = getPoint(fromNode, 'bottom');
                const end = getPoint(toNode, 'top');

                if (!start || !end) {
                    return;
                }

                const midY = start.y + Math.max(20, (end.y - start.y) / 2);
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

                path.setAttribute('d', `M ${start.x} ${start.y} C ${start.x} ${midY}, ${end.x} ${midY}, ${end.x} ${end.y}`);
                path.setAttribute('class', `pedigree-line-path ${cssClass}`.trim());

                svg.appendChild(path);
            }

            connect('FF', 'F');
            connect('FM', 'F');
            connect('MF', 'M');
            connect('MM', 'M');
            connect('F', 'CURRENT', 'strong');
            connect('M', 'CURRENT', 'strong');
        }

        function redrawPedigreeTrees(delay = 0) {
            const draw = function () {
                document.querySelectorAll('.js-pedigree-tree').forEach(function (tree) {
                    drawPedigreeLines(tree);
                });
            };

            if (delay) {
                window.setTimeout(draw, delay);
                return;
            }

            draw();
        }

        document.addEventListener('DOMContentLoaded', function () {
            redrawPedigreeTrees(100);

            document.querySelectorAll('.js-pedigree-tree img').forEach(function (img) {
                img.addEventListener('load', function () {
                    const tree = img.closest('.js-pedigree-tree');

                    if (tree) {
                        drawPedigreeLines(tree);
                    }
                }, { once: true });
            });
        });

        window.addEventListener('resize', function () {
            redrawPedigreeTrees();
        });
    </script>
@endpush
