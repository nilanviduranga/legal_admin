<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GazetteResource;
use App\Models\Gazette;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicGazetteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Gazette::query()->orderByDesc('gazette_date');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return GazetteResource::collection($query->paginate(50));
    }

    public function show(Gazette $gazette): JsonResponse
    {
        return response()->json(new GazetteResource($gazette->load('acts')));
    }
}
