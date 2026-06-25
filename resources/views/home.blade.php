@extends('layouts.app')

@section('subtitle', 'Dashboard')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-chart-line"></i>
                </span>
                <div>
                    <h1 class="module-title">Panel Administrativo</h1>
                    <p class="module-subtitle">
                        Centro de gestión para criaderos, ganado, genealogía, sanidad y certificación.
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content_body')
    <div class="dashboard-welcome mb-4">
        <h1>Bienvenido a PERU ASOCEBU</h1>
        <p>
            Gestiona la información ganadera desde un entorno centralizado, seguro y preparado para la trazabilidad.
        </p>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body dashboard-stat">
                    <span class="dashboard-stat-icon"><i class="fas fa-warehouse"></i></span>
                    <div>
                        <div class="dashboard-stat-label">Gestión institucional</div>
                        <h2 class="dashboard-stat-value">Criaderos y Haciendas</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body dashboard-stat">
                    <span class="dashboard-stat-icon"><i class="fas fa-cow"></i></span>
                    <div>
                        <div class="dashboard-stat-label">Registro ganadero</div>
                        <h2 class="dashboard-stat-value">Ganado y Genealogía</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body dashboard-stat">
                    <span class="dashboard-stat-icon"><i class="fas fa-certificate"></i></span>
                    <div>
                        <div class="dashboard-stat-label">Trazabilidad</div>
                        <h2 class="dashboard-stat-value">Control y Certificados</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
