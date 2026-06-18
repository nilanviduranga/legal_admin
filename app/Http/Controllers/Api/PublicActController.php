<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActResource;
use App\Models\Act;
use App\Services\LegalDocumentParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicActController extends Controller
{
    public function __construct(private readonly LegalDocumentParser $parser) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Act::query()->with('gazette')->orderBy('act_year')->orderBy('act_no');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return ActResource::collection($query->paginate(50));
    }

    public function show(Act $act): JsonResponse
    {
        return response()->json(new ActResource($act->load('gazette')));
    }

    public function tree(Act $act): JsonResponse
    {
        return response()->json([
            'act_id' => $act->act_id,
            'title' => $act->title,
            'status' => $act->status,
            'nodes' => $this->parser->buildTreeJson($act->act_id),
        ]);
    }
}
