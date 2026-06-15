<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LegalNodeApiController extends Controller
{
    /**
     * GET /api/v1/nodes/leaf
     * Returns all leaf nodes (has_children = false).
     * Optional query param: act_id to filter by act.
     * Used by the AI agent to discover every concrete provision it can look up.
     */
    public function leafNodes(Request $request): JsonResponse
    {
        $query = LegalNode::query()
            ->where('has_children', false)
            // ->select(['node_id', 'act_id', 'parent_node_id', 'node_type', 'node_no', 'heading', 'level', 'status'])
            ->select(['node_id'])
            ->orderBy('act_id')
            ->orderBy('level')
            ->orderBy('sort_order');

        if ($request->filled('act_id')) {
            $query->where('act_id', (int) $request->query('act_id'));
        }

        return response()->json($query->get());
    }

    /**
     * GET /api/v1/nodes/{node}/law-path
     * Walks from the given node up to the root and returns the full ancestor chain.
     * The `law_text` field is the concatenated, ready-to-inject string for AI prompts.
     */
    public function lawPath(LegalNode $node): JsonResponse
    {
        $ancestors = [];
        $current = $node;

        while ($current) {
            $label = trim($current->node_type . ($current->node_no ? ' ' . $current->node_no : ''));
            $body = trim(($current->heading ? $current->heading . "\n" : '') . ($current->text_content ?? ''));

            array_unshift($ancestors, [
                'node_id'    => $current->node_id,
                'node_type'  => $current->node_type,
                'node_no'    => $current->node_no,
                'heading'    => $current->heading,
                'text_content' => $current->text_content,
                'level'      => $current->level,
                'label'      => $label,
                'segment'    => $label . "\n" . $body,
            ]);

            $current = $current->parent_node_id ? $current->parent : null;
        }

        return response()->json([
            'node_id'   => $node->node_id,
            // 'act_id'    => $node->act_id,
            // 'ancestors' => $ancestors,
            'law_text'  => implode("\n\n", array_column($ancestors, 'segment')),
        ]);
    }

    public function children(LegalNode $node): JsonResponse
    {
        $children = $node->children()->get();
        return response()->json($children);
    }

    public function update(Request $request, LegalNode $node): JsonResponse
    {
        $validated = $request->validate([
            'text_content' => 'nullable|string',
            'heading' => 'nullable|string|max:500',
            'status' => 'sometimes|in:active,repealed,amended',
        ]);

        $node->update($validated);
        return response()->json($node->fresh());
    }

    public function references(LegalNode $node): JsonResponse
    {
        $outgoing = $node->outgoingReferences()
            ->with(['targetNode'])
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
            ->with(['sourceNode'])
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
