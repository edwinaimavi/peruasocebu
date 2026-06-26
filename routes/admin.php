<?php

use App\Http\Controllers\Admin\DocumentLookupController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\RanchController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Rutas para la gestión de usuarios en el panel de administración|
Route::get('users/list', [UserController::class, 'list'])->name('users.list');
Route::resource('users', UserController::class)->except(['create', 'show']);

Route::get('roles/list', [RoleController::class, 'list'])->name('roles.list');
Route::get('roles/{role}/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');
Route::resource('roles', RoleController::class)->except(['create', 'show']);

Route::get('ranches/list', [RanchController::class, 'list'])->name('ranches.list');
Route::resource('ranches', RanchController::class)->except(['create', 'edit']);

Route::get('documentos/consultar/{numero}', [DocumentLookupController::class, 'consultarDocumento'])
    ->name('documents.consult');

Route::get('owners/list', [OwnerController::class, 'list'])->name('owners.list');
Route::resource('owners', OwnerController::class)->except(['create', 'edit']);
