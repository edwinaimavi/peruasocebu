<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicSearchController;
use App\Models\BlogPost;
use App\Models\Breed;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $latestPosts = Schema::hasTable('blog_posts')
        ? BlogPost::published()
            ->with('author')
            ->latest('published_at')
            ->take(3)
            ->get()
        : collect();

    $breeds = Schema::hasTable('breeds')
        ? Breed::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->take(6)
            ->get(['name', 'description', 'characteristics', 'code', 'origin_country', 'image_path', 'status'])
        : collect();

    return view('public.home', [
        'latestPosts' => $latestPosts,
        'breeds' => $breeds,
    ]);
})->name('public.home');

Route::get('/blog', [BlogController::class, 'index'])->name('public.blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('public.blog.show');

Route::get('/consulta', [PublicSearchController::class, 'search'])->name('public.search');

Route::post('/contacto/enviar', [ContactController::class, 'store'])
    ->name('public.contact.store');

Route::get('/certificados/verificar/{code?}', [CertificateVerificationController::class, 'show'])
    ->name('certificates.verify');

Route::redirect('dashboard', 'admin/dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__ . '/auth.php';

Auth::routes();

Route::redirect('/home', '/admin/dashboard')
    ->middleware('auth')
    ->name('home');
