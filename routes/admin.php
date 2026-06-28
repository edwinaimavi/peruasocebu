<?php

use App\Http\Controllers\Admin\BreedController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CattleController;
use App\Http\Controllers\Admin\CattleGenealogyLinkController;
use App\Http\Controllers\Admin\CattlePhotoController;
use App\Http\Controllers\Admin\CattleSaleController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CertificateSignatureController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DocumentLookupController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\OwnershipHistoryController;
use App\Http\Controllers\Admin\RanchController;
use App\Http\Controllers\Admin\ReproductionRecordController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VaccinationController;
use App\Http\Controllers\Admin\VeterinaryRecordController;
use App\Http\Controllers\Admin\VeterinarianController;
use App\Http\Controllers\Admin\WeightRecordController;
use Illuminate\Support\Facades\Route;

// Rutas para la gestión de usuarios en el panel de administración|
Route::get('users/list', [UserController::class, 'list'])->name('users.list');
Route::resource('users', UserController::class)->except(['create', 'show']);

Route::get('roles/list', [RoleController::class, 'list'])->name('roles.list');
Route::get('roles/{role}/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');
Route::resource('roles', RoleController::class)->except(['create', 'show']);

Route::get('blog-posts/list', [BlogPostController::class, 'list'])->name('blog-posts.list');
Route::post('blog-posts/{blogPost}/publish', [BlogPostController::class, 'publish'])->name('blog-posts.publish');
Route::post('blog-posts/{blogPost}/draft', [BlogPostController::class, 'draft'])->name('blog-posts.draft');
Route::resource('blog-posts', BlogPostController::class)
    ->parameters(['blog-posts' => 'blogPost'])
    ->except(['create', 'edit']);

Route::get('contact-messages/list', [ContactMessageController::class, 'list'])->name('contact-messages.list');
Route::post('contact-messages/{contactMessage}/mark-read', [ContactMessageController::class, 'markRead'])->name('contact-messages.mark-read');
Route::post('contact-messages/{contactMessage}/mark-answered', [ContactMessageController::class, 'markAnswered'])->name('contact-messages.mark-answered');
Route::post('contact-messages/{contactMessage}/mark-new', [ContactMessageController::class, 'markNew'])->name('contact-messages.mark-new');
Route::resource('contact-messages', ContactMessageController::class)
    ->parameters(['contact-messages' => 'contactMessage'])
    ->only(['index', 'show', 'destroy']);

Route::get('ranches/list', [RanchController::class, 'list'])->name('ranches.list');
Route::resource('ranches', RanchController::class)->except(['create', 'edit']);

Route::get('documentos/consultar/{numero}', [DocumentLookupController::class, 'consultarDocumento'])
    ->name('documents.consult');

Route::get('owners/list', [OwnerController::class, 'list'])->name('owners.list');
Route::resource('owners', OwnerController::class)->except(['create', 'edit']);

Route::get('veterinarians/list', [VeterinarianController::class, 'list'])->name('veterinarians.list');
Route::resource('veterinarians', VeterinarianController::class)->except(['create', 'edit']);

Route::get('breeds/list', [BreedController::class, 'list'])->name('breeds.list');
Route::resource('breeds', BreedController::class)->except(['create', 'edit']);

Route::get('cattle/list', [CattleController::class, 'list'])->name('cattle.list');
Route::get('cattle/{cattle}/photos', [CattlePhotoController::class, 'listByCattle'])->name('cattle.photos.list');
Route::post('cattle/{cattle}/photos', [CattlePhotoController::class, 'store'])->name('cattle.photos.store');
Route::get('cattle-photos/{photo}', [CattlePhotoController::class, 'show'])->name('cattle.photos.show');
Route::post('cattle-photos/{photo}', [CattlePhotoController::class, 'update'])->name('cattle.photos.update');
Route::delete('cattle-photos/{photo}', [CattlePhotoController::class, 'destroy'])->name('cattle.photos.destroy');
Route::post('cattle-photos/{photo}/main', [CattlePhotoController::class, 'setMain'])->name('cattle.photos.main');
Route::resource('cattle', CattleController::class)->except(['create', 'edit']);

Route::get('cattle-genealogy/list', [CattleGenealogyLinkController::class, 'list'])->name('cattle-genealogy.list');
Route::resource('cattle-genealogy', CattleGenealogyLinkController::class)
    ->parameters(['cattle-genealogy' => 'cattleGenealogyLink'])
    ->except(['create', 'edit']);

Route::get('ownership-histories/list', [OwnershipHistoryController::class, 'list'])->name('ownership-histories.list');
Route::resource('ownership-histories', OwnershipHistoryController::class)
    ->parameters(['ownership-histories' => 'ownershipHistory'])
    ->except(['create', 'edit']);

Route::get('cattle-sales/list', [CattleSaleController::class, 'list'])->name('cattle-sales.list');
Route::resource('cattle-sales', CattleSaleController::class)
    ->parameters(['cattle-sales' => 'cattleSale'])
    ->except(['create', 'edit']);

Route::get('certificates/list', [CertificateController::class, 'list'])->name('certificates.list');
Route::get('certificates/cattle-info/{cattle}', [CertificateController::class, 'cattleInfo'])->name('certificates.cattle-info');
Route::get('certificates/{certificate}/pdf', [CertificateController::class, 'downloadPdf'])->name('certificates.pdf');
Route::post('certificates/{certificate}/regenerate-pdf', [CertificateController::class, 'regeneratePdf'])->name('certificates.regenerate-pdf');
Route::post('certificates/{certificate}/cancel', [CertificateController::class, 'cancel'])->name('certificates.cancel');
Route::get('certificates/{certificate}/signatures', [CertificateSignatureController::class, 'listByCertificate'])->name('certificates.signatures');
Route::post('certificates/{certificate}/signatures', [CertificateSignatureController::class, 'storeByCertificate'])->name('certificates.signatures.store');
Route::resource('certificates', CertificateController::class)->except(['create', 'edit']);

Route::get('certificate-signatures/list', [CertificateSignatureController::class, 'list'])->name('certificate-signatures.list');
Route::resource('certificate-signatures', CertificateSignatureController::class)
    ->parameters(['certificate-signatures' => 'certificateSignature'])
    ->except(['create', 'edit']);

Route::get('veterinary-records/list', [VeterinaryRecordController::class, 'list'])->name('veterinary-records.list');
Route::resource('veterinary-records', VeterinaryRecordController::class)
    ->parameters(['veterinary-records' => 'veterinaryRecord'])
    ->except(['create', 'edit']);

Route::get('vaccinations/list', [VaccinationController::class, 'list'])->name('vaccinations.list');
Route::resource('vaccinations', VaccinationController::class)->except(['create', 'edit']);

Route::get('treatments/list', [TreatmentController::class, 'list'])->name('treatments.list');
Route::resource('treatments', TreatmentController::class)->except(['create', 'edit']);

Route::get('weight-records/list', [WeightRecordController::class, 'list'])->name('weight-records.list');
Route::resource('weight-records', WeightRecordController::class)
    ->parameters(['weight-records' => 'weightRecord'])
    ->except(['create', 'edit']);

Route::get('reproduction-records/list', [ReproductionRecordController::class, 'list'])->name('reproduction-records.list');
Route::resource('reproduction-records', ReproductionRecordController::class)
    ->parameters(['reproduction-records' => 'reproductionRecord'])
    ->except(['create', 'edit']);
