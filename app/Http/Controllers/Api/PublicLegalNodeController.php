<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LegalNodeResource;
use App\Models\LegalNode;
use Illuminate\Http\JsonResponse;

class PublicLegalNodeController extends Controller
{
    public function show(LegalNode $node): JsonResponse
    {
        return response()->json(new LegalNodeResource($node->load('act')));
    }

    public function children(LegalNode $node): JsonResponse
    {
        return response()->json(LegalNodeResource::collection($node->children()->get()));
    }

    public function references(LegalNode $node): JsonResponse
    {
        $outgoing = $node->outgoingReferences()
            ->with('targetNode')
            ->get()
            ->map(fn ($ref) => [
                'reference_id' => $ref->reference_id,
                'reference_type' => $ref->reference_type,
                'reference_text' => $ref->reference_text,
                'target_node' => $ref->targetNode ? [
                    'node_id' => $ref->targetNode->node_id,
                    'node_no' => $ref->targetNode->node_no,
                    'heading' => $ref->targetNode->heading,
                    'full_path' => $ref->targetNode->getFullPath(),
                ] : null,
            ]);

        $incoming = $node->incomingReferences()
            ->with('sourceNode')
            ->get()
            ->map(fn ($ref) => [
                'reference_id' => $ref->reference_id,
                'reference_type' => $ref->reference_type,
                'reference_text' => $ref->reference_text,
                'source_node' => $ref->sourceNode ? [
                    'node_id' => $ref->sourceNode->node_id,
                    'node_no' => $ref->sourceNode->node_no,
                    'heading' => $ref->sourceNode->heading,
                    'full_path' => $ref->sourceNode->getFullPath(),
                ] : null,
            ]);

        return response()->json(compact('outgoing', 'incoming'));
    }
}
