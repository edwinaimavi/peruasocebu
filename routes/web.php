<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\ContactController;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.home', [
        'latestPosts' => BlogPost::published()
            ->with('author')
            ->latest('published_at')
            ->take(3)
            ->get(),
    ]);
})->name('public.home');

Route::get('/blog', [BlogController::class, 'index'])->name('public.blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('public.blog.show');

Route::post('/contacto/enviar', [ContactController::class, 'store'])
    ->name('public.contact.store');

Route::get('/certificados/verificar/{code}', [CertificateVerificationController::class, 'show'])
    ->name('certificates.verify');

Route::view('dashboard', 'dashboard')
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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
