<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ActApiController;
use App\Http\Controllers\Api\CrossReferenceApiController;
use App\Http\Controllers\Api\LegalNodeApiController;
use App\Http\Controllers\Api\PublicActController;
use App\Http\Controllers\Api\PublicGazetteController;
use App\Http\Controllers\Api\PublicLegalNodeController;
use App\Http\Controllers\Api\PublicSearchController;
use Illuminate\Support\Facades\Route;

// ─── Internal API (used by the admin panel / AI agent) ─────────────────────
Route::prefix('v1')->group(function () {
    // Act tree
    Route::get('acts/{act}/tree', [ActApiController::class, 'tree']);

    // Node operations
    Route::get('nodes/leaf', [LegalNodeApiController::class, 'leafNodes']);
    Route::get('nodes/{node}/children', [LegalNodeApiController::class, 'children']);
    Route::get('nodes/{node}/law-path', [LegalNodeApiController::class, 'lawPath']);
    Route::put('nodes/{node}', [LegalNodeApiController::class, 'update']);
    Route::get('nodes/{node}/references', [LegalNodeApiController::class, 'references']);

    // Cross references
    Route::post('cross-references', [CrossReferenceApiController::class, 'store']);
    Route::delete('cross-references/{crossReference}', [CrossReferenceApiController::class, 'destroy']);
});

// ─── Public API (read-only, no auth — for external sites) ──────────────────
// See docs/PUBLIC_API.md for full documentation.
Route::prefix('public/v1')->group(function () {
    // Acts
    Route::get('acts', [PublicActController::class, 'index']);
    Route::get('acts/{act}', [PublicActController::class, 'show']);
    Route::get('acts/{act}/tree', [PublicActController::class, 'tree']);

    // Gazettes
    Route::get('gazettes', [PublicGazetteController::class, 'index']);
    Route::get('gazettes/{gazette}', [PublicGazetteController::class, 'show']);

    // Sections (legal nodes) and their relations
    Route::get('nodes/{node}', [PublicLegalNodeController::class, 'show']);
    Route::get('nodes/{node}/children', [PublicLegalNodeController::class, 'children']);
    Route::get('nodes/{node}/references', [PublicLegalNodeController::class, 'references']);

    // Full-text search across sections
    Route::get('search', [PublicSearchController::class, 'search']);
});
