<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ActApiController;
use App\Http\Controllers\Api\CrossReferenceApiController;
use App\Http\Controllers\Api\LegalNodeApiController;
use Illuminate\Support\Facades\Route;

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
