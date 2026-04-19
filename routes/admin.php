<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\Admin\PageController;

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;


//Rutas para la gestión de usuarios en el panel de administración|
Route::get('users/list', [UserController::class, 'list'])->name('users.list');
Route::resource('users', UserController::class)->except(['create', 'show']);

Route::get('roles/list', [RoleController::class, 'list'])->name('roles.list');
Route::get('roles/{role}/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');
Route::resource('roles', RoleController::class)->except(['create', 'show']);




//RUTAS PARA SUCURSALES
Route::get('branches/list', [BranchController::class, 'list'])->name('branches.list');
Route::resource('branches', BranchController::class)->except(['create', 'show']);

//RUTAS PARA LAS CATEGORIAS
Route::get('categories/list', [CategoryController::class, 'list'])->name('categories.list');
Route::resource('categories', CategoryController::class)->except(['create', 'show']);


//RUTAS PARA POST
Route::get('posts/list', [PostController::class, 'list'])->name('posts.list');
// routes/web.php
Route::get('/admin/posts/{post}/meta', [PostController::class, 'meta'])
    ->name('posts.meta');

Route::get('/admin/posts/{post}/view', [PostController::class, 'showView'])
    ->name('posts.view');
Route::resource('posts', PostController::class)->except(['create', 'show']);


//RUTAS PARA PAGES 
Route::get('pages/list', [PageController::class, 'list'])->name('pages.list');
Route::get('/admin/pages/{page}/view', [PageController::class, 'showView'])
    ->name('pages.view');

Route::resource('pages', PageController::class)->except(['create', 'show']);

//RUTAS PARA PRODUCTS 
Route::get('products/list', [ProductController::class, 'list'])->name('products.list');
Route::get('/admin/products/{product}/view', [ProductController::class, 'showView'])
    ->name('products.view');
Route::resource('products', ProductController::class)->except(['create', 'show']);



