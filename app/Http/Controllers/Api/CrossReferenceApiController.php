<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCrossReferenceRequest;
use App\Models\CrossReference;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CrossReferenceApiController extends Controller
{
    public function store(StoreCrossReferenceRequest $request): JsonResponse
    {
        $ref = DB::transaction(fn () => CrossReference::create($request->validated()));
        return response()->json($ref->load(['sourceNode', 'targetNode']), 201);
    }

    public function destroy(CrossReference $crossReference): JsonResponse
    {
        DB::transaction(fn () => $crossReference->delete());
        return response()->json(null, 204);
    }
}
