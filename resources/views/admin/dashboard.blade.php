@extends('layouts.app')

@section('subtitle', 'Panel Administrativo')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-chart-pie"></i>
                </span>
                <div>
                    <h1 class="module-title">Panel Administrativo</h1>
                    <p class="module-subtitle">
                        Centro de gestion para criaderos, ganado, genealogia, sanidad y certificacion.
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content_body')
    <section class="admin-hero-card mb-4">
        <div>
            <span class="admin-hero-kicker">PERU ASOCEBU</span>
            <h2>Bienvenido al centro de gestion ganadera</h2>
            <p>
                Supervisa criaderos, ganado, sanidad, genealogia, certificados y mensajes publicos desde un
                tablero seguro y centralizado.
            </p>
        </div>
        <div class="admin-hero-emblem">
            <i class="fas fa-cow"></i>
        </div>
    </section>

    <div class="row">
        @foreach ($stats as $stat)
            <div class="col-xl-3 col-md-6 mb-3">
                <article class="admin-stat-card">
                    <span class="admin-stat-icon admin-stat-icon-{{ $stat['tone'] }}">
                        <i class="{{ $stat['icon'] }}"></i>
                    </span>
                    <div>
                        <span class="admin-stat-label">{{ $stat['label'] }}</span>
                        <strong class="admin-stat-number">{{ number_format($stat['value']) }}</strong>
                    </div>
                </article>
            </div>
        @endforeach
    </div>

    <section class="admin-section-card mb-4">
        <div class="admin-section-heading">
            <div>
                <h3>Accesos rapidos</h3>
                <p>Atajos hacia las tareas administrativas mas usadas.</p>
            </div>
        </div>
        <div class="quick-action-grid">
            @foreach ($quickActions as $action)
                @if (Route::has($action['route']))
                    <a class="quick-action-card" href="{{ route($action['route']) }}">
                        <span><i class="{{ $action['icon'] }}"></i></span>
                        <strong>{{ $action['label'] }}</strong>
                        <small>{{ $action['description'] }}</small>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    <div class="row">
        <div class="col-xl-6 mb-4">
            <section class="admin-section-card h-100">
                <div class="admin-section-heading">
                    <div>
                        <h3>Ultimos ganados registrados</h3>
                        <p>Registros recientes del hato.</p>
                    </div>
                    @if (Route::has('admin.cattle.index'))
                        <a href="{{ route('admin.cattle.index') }}">Ver todos</a>
                    @endif
                </div>
                <div class="admin-list">
                    @forelse ($latestCattle as $cattle)
                        <div class="admin-list-item">
                            <span class="admin-list-icon"><i class="fas fa-paw"></i></span>
                            <div>
                                <strong>{{ $cattle->name ?: 'Sin nombre' }}</strong>
                                <small>{{ $cattle->code }} · {{ $cattle->breed?->name ?: 'Sin raza' }} · {{ $cattle->ranch?->name ?: 'Sin criadero' }}</small>
                            </div>
                            <span class="admin-mini-badge">{{ $cattle->sale_status ?: 'N/D' }}</span>
                        </div>
                    @empty
                        <p class="admin-empty">Aun no hay registros disponibles.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-xl-6 mb-4">
            <section class="admin-section-card h-100">
                <div class="admin-section-heading">
                    <div>
                        <h3>Ultimos certificados emitidos</h3>
                        <p>Certificados y verificaciones recientes.</p>
                    </div>
                    @if (Route::has('admin.certificates.index'))
                        <a href="{{ route('admin.certificates.index') }}">Ver todos</a>
                    @endif
                </div>
                <div class="admin-list">
                    @forelse ($latestCertificates as $certificate)
                        <div class="admin-list-item">
                            <span class="admin-list-icon"><i class="fas fa-certificate"></i></span>
                            <div>
                                <strong>{{ $certificate->certificate_number }}</strong>
                                <small>{{ $certificate->cattle?->code ?: 'Sin ganado' }} · {{ $certificate->issue_date?->format('d/m/Y') ?: 'Sin fecha' }}</small>
                            </div>
                            <span class="admin-mini-badge">{{ $certificate->status ?: 'N/D' }}</span>
                        </div>
                    @empty
                        <p class="admin-empty">Aun no hay registros disponibles.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 mb-4">
            <section class="admin-section-card h-100">
                <div class="admin-section-heading">
                    <div>
                        <h3>Alertas sanitarias</h3>
                        <p>Vacunas y visitas veterinarias proximas.</p>
                    </div>
                </div>

                <div class="admin-alert-block">
                    <h4><i class="fas fa-syringe mr-1"></i> Vacunas proximas</h4>
                    @forelse ($upcomingVaccinations as $vaccination)
                        <div class="admin-alert-item">
                            <strong>{{ $vaccination->vaccine_name }}</strong>
                            <span>{{ $vaccination->cattle?->code ?: 'Sin ganado' }} · vence {{ $vaccination->next_due_date?->format('d/m/Y') }}</span>
                        </div>
                    @empty
                        <p class="admin-empty">No hay vacunas proximas en los siguientes 30 dias.</p>
                    @endforelse
                </div>

                <div class="admin-alert-block mt-3">
                    <h4><i class="fas fa-notes-medical mr-1"></i> Visitas veterinarias</h4>
                    @forelse ($upcomingVeterinaryVisits as $record)
                        <div class="admin-alert-item">
                            <strong>{{ $record->cattle?->code ?: 'Sin ganado' }}</strong>
                            <span>{{ $record->veterinarian?->full_name ?: 'Sin veterinario' }} · {{ $record->next_visit_date?->format('d/m/Y') }}</span>
                        </div>
                    @empty
                        <p class="admin-empty">No hay visitas proximas en los siguientes 30 dias.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-xl-6 mb-4">
            <section class="admin-section-card h-100">
                <div class="admin-section-heading">
                    <div>
                        <h3>Mensajes de contacto</h3>
                        <p>Ultimos mensajes enviados desde la pagina publica.</p>
                    </div>
                    @if (Route::has('admin.contact-messages.index'))
                        <a href="{{ route('admin.contact-messages.index') }}">Ver todos</a>
                    @endif
                </div>
                <div class="admin-list">
                    @forelse ($latestContactMessages as $message)
                        <div class="admin-list-item">
                            <span class="admin-list-icon"><i class="fas fa-envelope"></i></span>
                            <div>
                                <strong>{{ $message->full_name }}</strong>
                                <small>{{ $message->subject ?: 'Sin asunto' }} · {{ $message->created_at?->format('d/m/Y H:i') }}</small>
                            </div>
                            <span class="admin-mini-badge">{{ $message->status }}</span>
                        </div>
                    @empty
                        <p class="admin-empty">Aun no hay registros disponibles.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 mb-4">
            <section class="admin-section-card h-100">
                <div class="admin-section-heading">
                    <div>
                        <h3>Revisiones veterinarias recientes</h3>
                        <p>Ultimos controles sanitarios registrados.</p>
                    </div>
                    @if (Route::has('admin.veterinary-records.index'))
                        <a href="{{ route('admin.veterinary-records.index') }}">Ver todos</a>
                    @endif
                </div>
                <div class="admin-list">
                    @forelse ($latestVeterinaryRecords as $record)
                        <div class="admin-list-item">
                            <span class="admin-list-icon"><i class="fas fa-stethoscope"></i></span>
                            <div>
                                <strong>{{ $record->cattle?->code ?: 'Sin ganado' }}</strong>
                                <small>{{ $record->record_type ?: 'Revision' }} · {{ $record->record_date?->format('d/m/Y') ?: 'Sin fecha' }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="admin-empty">Aun no hay registros disponibles.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-xl-6 mb-4">
            <section class="admin-section-card h-100">
                <div class="admin-section-heading">
                    <div>
                        <h3>Blog / Noticias</h3>
                        <p>Publicaciones recientes del sitio publico.</p>
                    </div>
                    @if (Route::has('admin.blog-posts.index'))
                        <a href="{{ route('admin.blog-posts.index') }}">Ver todos</a>
                    @endif
                </div>
                <div class="admin-list">
                    @forelse ($recentBlogPosts as $post)
                        <div class="admin-list-item">
                            <span class="admin-list-icon"><i class="fas fa-newspaper"></i></span>
                            <div>
                                <strong>{{ $post->title }}</strong>
                                <small>{{ $post->status }} · {{ $post->published_at?->format('d/m/Y H:i') ?: 'Sin publicar' }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="admin-empty">Aun no hay registros disponibles.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@stop

@push('css')
    <style>
        .admin-hero-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding: clamp(22px, 4vw, 34px);
            color: #fff;
            background:
                radial-gradient(circle at 86% 18%, rgba(200, 155, 60, .34), transparent 28%),
                linear-gradient(135deg, #123524 0%, #1f4d36 100%);
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(18, 53, 36, .18);
        }

        .admin-hero-kicker {
            color: #dfc184;
            font-size: .72rem;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .admin-hero-card h2 {
            margin: .45rem 0 .6rem;
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(1.7rem, 4vw, 2.65rem);
            font-weight: 500;
        }

        .admin-hero-card p {
            max-width: 760px;
            margin: 0;
            color: rgba(255, 255, 255, .78);
            font-size: .98rem;
        }

        .admin-hero-emblem {
            display: grid;
            width: 86px;
            height: 86px;
            flex: 0 0 86px;
            place-items: center;
            color: #dfc184;
            background: rgba(255, 255, 255, .09);
            border: 1px solid rgba(223, 193, 132, .2);
            border-radius: 24px;
            font-size: 2rem;
        }

        .admin-stat-card,
        .admin-section-card {
            background: #fff;
            border: 1px solid rgba(18, 53, 36, .08);
            border-radius: 20px;
            box-shadow: 0 14px 35px rgba(18, 53, 36, .07);
        }

        .admin-stat-card {
            display: flex;
            height: 100%;
            align-items: center;
            gap: 1rem;
            padding: 20px;
        }

        .admin-stat-icon,
        .admin-list-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #c89b3c;
            background: #f7f3ea;
        }

        .admin-stat-icon {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            border-radius: 14px;
            font-size: 1.08rem;
        }

        .admin-stat-icon-green {
            color: #1f4d36;
            background: #eaf2ed;
        }

        .admin-stat-icon-blue {
            color: #245c70;
            background: #e8f3f6;
        }

        .admin-stat-label {
            display: block;
            color: #6b7280;
            font-size: .74rem;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .admin-stat-number {
            display: block;
            margin-top: .15rem;
            color: #123524;
            font-size: 1.85rem;
            font-weight: 900;
            line-height: 1;
        }

        .admin-section-card {
            padding: 22px;
        }

        .admin-section-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 16px;
        }

        .admin-section-heading h3 {
            margin: 0;
            color: #123524;
            font-size: 1.08rem;
            font-weight: 900;
        }

        .admin-section-heading p {
            margin: .2rem 0 0;
            color: #6b7280;
            font-size: .82rem;
        }

        .admin-section-heading a {
            color: #1f4d36;
            font-size: .78rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .quick-action-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .quick-action-card {
            display: grid;
            gap: 8px;
            min-height: 126px;
            padding: 16px;
            color: #123524;
            background: #fbfaf6;
            border: 1px solid rgba(18, 53, 36, .08);
            border-radius: 16px;
            text-decoration: none;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .quick-action-card:hover {
            color: #123524;
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(18, 53, 36, .1);
        }

        .quick-action-card span {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            color: #c89b3c;
            background: #fff;
            border-radius: 12px;
        }

        .quick-action-card strong {
            font-size: .88rem;
            font-weight: 900;
        }

        .quick-action-card small {
            color: #6b7280;
            font-size: .75rem;
        }

        .admin-list {
            display: grid;
            gap: 11px;
        }

        .admin-list-item {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            padding: 12px;
            background: #fbfaf6;
            border: 1px solid rgba(18, 53, 36, .06);
            border-radius: 14px;
        }

        .admin-list-icon {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            border-radius: 12px;
        }

        .admin-list-item > div {
            min-width: 0;
            flex: 1 1 auto;
        }

        .admin-list-item strong,
        .admin-list-item small {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-list-item strong {
            color: #123524;
            font-size: .88rem;
        }

        .admin-list-item small {
            color: #6b7280;
            font-size: .76rem;
        }

        .admin-mini-badge {
            flex: 0 0 auto;
            padding: .3rem .55rem;
            color: #1f4d36;
            background: #eaf2ed;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        .admin-alert-block h4 {
            margin: 0 0 10px;
            color: #123524;
            font-size: .9rem;
            font-weight: 900;
        }

        .admin-alert-item {
            display: grid;
            gap: 2px;
            padding: 10px 12px;
            background: #fff8ea;
            border: 1px solid rgba(200, 155, 60, .18);
            border-radius: 12px;
        }

        .admin-alert-item + .admin-alert-item {
            margin-top: 8px;
        }

        .admin-alert-item strong {
            color: #7a5812;
            font-size: .84rem;
        }

        .admin-alert-item span,
        .admin-empty {
            color: #6b7280;
            font-size: .78rem;
        }

        .admin-empty {
            margin: 0;
            padding: 12px;
            background: #fbfaf6;
            border-radius: 12px;
        }

        @media (max-width: 1199.98px) {
            .quick-action-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .admin-hero-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .admin-hero-emblem {
                display: none;
            }

            .quick-action-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .quick-action-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
