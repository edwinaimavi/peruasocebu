<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Cattle;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Owner;
use App\Models\Ranch;
use App\Models\Vaccination;
use App\Models\Veterinarian;
use App\Models\VeterinaryRecord;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->stats(),
            'quickActions' => $this->quickActions(),
            'latestCattle' => $this->latestCattle(),
            'latestCertificates' => $this->latestCertificates(),
            'latestVeterinaryRecords' => $this->latestVeterinaryRecords(),
            'latestContactMessages' => $this->latestContactMessages(),
            'upcomingVaccinations' => $this->upcomingVaccinations(),
            'upcomingVeterinaryVisits' => $this->upcomingVeterinaryVisits(),
            'recentBlogPosts' => $this->recentBlogPosts(),
        ]);
    }

    private function stats(): array
    {
        $totalCattle = $this->tableExists('cattle') ? Cattle::count() : 0;
        $availableCattle = $this->tableExists('cattle') ? Cattle::where('sale_status', 'available')->count() : 0;
        $soldCattle = $this->tableExists('cattle') ? Cattle::where('sale_status', 'sold')->count() : 0;

        return [
            ['label' => 'Ganado registrado', 'value' => $totalCattle, 'icon' => 'fas fa-paw', 'tone' => 'green'],
            ['label' => 'Ganado disponible', 'value' => $availableCattle, 'icon' => 'fas fa-check-circle', 'tone' => 'gold'],
            ['label' => 'Ganado vendido', 'value' => $soldCattle, 'icon' => 'fas fa-handshake', 'tone' => 'blue'],
            ['label' => 'Criaderos / Haciendas', 'value' => $this->countTable(Ranch::class, 'ranches'), 'icon' => 'fas fa-warehouse', 'tone' => 'green'],
            ['label' => 'Propietarios', 'value' => $this->countTable(Owner::class, 'owners'), 'icon' => 'fas fa-users', 'tone' => 'gold'],
            ['label' => 'Veterinarios', 'value' => $this->countTable(Veterinarian::class, 'veterinarians'), 'icon' => 'fas fa-user-md', 'tone' => 'blue'],
            ['label' => 'Certificados emitidos', 'value' => $this->tableExists('certificates') ? Certificate::where('status', 'issued')->count() : 0, 'icon' => 'fas fa-certificate', 'tone' => 'green'],
            ['label' => 'Mensajes nuevos', 'value' => $this->tableExists('contact_messages') ? ContactMessage::where('status', 'new')->count() : 0, 'icon' => 'fas fa-envelope', 'tone' => 'gold'],
        ];
    }

    private function quickActions(): array
    {
        return [
            ['label' => 'Nuevo Ganado', 'route' => 'admin.cattle.index', 'icon' => 'fas fa-paw', 'description' => 'Registrar o revisar ganado'],
            ['label' => 'Nuevo Propietario', 'route' => 'admin.owners.index', 'icon' => 'fas fa-user-plus', 'description' => 'Gestionar propietarios'],
            ['label' => 'Revision Veterinaria', 'route' => 'admin.veterinary-records.index', 'icon' => 'fas fa-stethoscope', 'description' => 'Control sanitario'],
            ['label' => 'Nueva Vacuna', 'route' => 'admin.vaccinations.index', 'icon' => 'fas fa-syringe', 'description' => 'Programar vacunas'],
            ['label' => 'Nuevo Certificado', 'route' => 'admin.certificates.index', 'icon' => 'fas fa-certificate', 'description' => 'Emitir certificados'],
            ['label' => 'Nueva Publicacion', 'route' => 'admin.blog-posts.index', 'icon' => 'fas fa-newspaper', 'description' => 'Actualizar noticias'],
        ];
    }

    private function latestCattle()
    {
        if (! $this->tableExists('cattle')) {
            return collect();
        }

        return Cattle::with(['breed', 'ranch'])
            ->latest('id')
            ->take(5)
            ->get();
    }

    private function latestCertificates()
    {
        if (! $this->tableExists('certificates')) {
            return collect();
        }

        return Certificate::with('cattle')
            ->latest('id')
            ->take(5)
            ->get();
    }

    private function latestVeterinaryRecords()
    {
        if (! $this->tableExists('veterinary_records')) {
            return collect();
        }

        return VeterinaryRecord::with(['cattle', 'veterinarian'])
            ->latest('id')
            ->take(5)
            ->get();
    }

    private function latestContactMessages()
    {
        if (! $this->tableExists('contact_messages')) {
            return collect();
        }

        return ContactMessage::latest('id')
            ->take(5)
            ->get();
    }

    private function upcomingVaccinations()
    {
        if (! $this->tableExists('vaccinations')) {
            return collect();
        }

        return Vaccination::with('cattle')
            ->whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->orderBy('next_due_date')
            ->take(5)
            ->get();
    }

    private function upcomingVeterinaryVisits()
    {
        if (! $this->tableExists('veterinary_records')) {
            return collect();
        }

        return VeterinaryRecord::with(['cattle', 'veterinarian'])
            ->whereNotNull('next_visit_date')
            ->whereBetween('next_visit_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->orderBy('next_visit_date')
            ->take(5)
            ->get();
    }

    private function recentBlogPosts()
    {
        if (! $this->tableExists('blog_posts')) {
            return collect();
        }

        return BlogPost::with('author')
            ->latest('id')
            ->take(4)
            ->get();
    }

    private function countTable(string $model, string $table): int
    {
        return $this->tableExists($table) ? $model::count() : 0;
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }
}
