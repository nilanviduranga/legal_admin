<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ActController;
use App\Http\Controllers\Admin\CaseLawController;
use App\Http\Controllers\Admin\CrossReferenceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GazetteController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\LegalNodeController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StructureController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// ─── Root redirect ──────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('admin.dashboard'));

// ─── Auth ───────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

// ─── Admin panel ─────────────────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'ensure_admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Gazettes
        Route::resource('gazettes', GazetteController::class)
            ->parameters(['gazettes' => 'gazette']);

        // Acts
        Route::resource('acts', ActController::class)
            ->parameters(['acts' => 'act']);

        // Case Laws
        Route::resource('case-laws', CaseLawController::class)
            ->parameters(['case-laws' => 'caseLaw'])
            ->except(['create', 'edit']);

        // Legal nodes (shallow nested + standalone)
        Route::get('acts/{act}/nodes', [LegalNodeController::class, 'index'])->name('acts.nodes.index');
        Route::get('nodes/create', [LegalNodeController::class, 'create'])->name('nodes.create');
        Route::get('nodes/by-act', [LegalNodeController::class, 'byAct'])->name('nodes.by-act');
        Route::post('nodes', [LegalNodeController::class, 'store'])->name('nodes.store');
        Route::get('nodes/{node}', [LegalNodeController::class, 'show'])->name('nodes.show');
        Route::get('nodes/{node}/edit', [LegalNodeController::class, 'edit'])->name('nodes.edit');
        Route::put('nodes/{node}', [LegalNodeController::class, 'update'])->name('nodes.update');
        Route::post('nodes/{node}/new-version', [LegalNodeController::class, 'newVersion'])->name('nodes.new-version');
        Route::delete('nodes/{node}', [LegalNodeController::class, 'destroy'])->name('nodes.destroy');

        // Cross-references
        Route::get('cross-references', [CrossReferenceController::class, 'allIndex'])->name('cross-references.index');
        Route::get('cross-references/create', [CrossReferenceController::class, 'create'])->name('cross-references.create');
        Route::get('cross-references/{crossReference}/edit', [CrossReferenceController::class, 'edit'])->name('cross-references.edit');
        Route::get('nodes/{node}/references', [CrossReferenceController::class, 'index'])->name('nodes.references');
        Route::post('cross-references', [CrossReferenceController::class, 'store'])->name('cross-references.store');
        Route::put('cross-references/{crossReference}', [CrossReferenceController::class, 'update'])->name('cross-references.update');
        Route::delete('cross-references/{crossReference}', [CrossReferenceController::class, 'destroy'])->name('cross-references.destroy');

        // Import
        Route::get('import', [ImportController::class, 'showImportForm'])->name('import.form');
        Route::post('import/process', [ImportController::class, 'processImport'])->name('import.process');
        Route::post('import/confirm', [ImportController::class, 'confirmImport'])->name('import.confirm');

        // Full structure browser
        Route::get('structure', [StructureController::class, 'index'])->name('structure');
        Route::get('structure/children', [StructureController::class, 'children'])->name('structure.children');
        Route::get('structure/search', [StructureController::class, 'search'])->name('structure.search');

        // Search
        Route::get('search', [SearchController::class, 'search'])->name('search');

        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings');

        // User management
        Route::resource('users', UserController::class)
            ->parameters(['users' => 'user']);
    });
