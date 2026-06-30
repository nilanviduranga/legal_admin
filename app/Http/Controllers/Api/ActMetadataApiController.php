<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\ActAiMetadata;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActMetadataApiController extends Controller
{
    /**
     * GET /api/v1/acts
     * List all active acts with basic fields.
     * Used by legal-rag to discover acts for metadata generation.
     */
    public function index(): JsonResponse
    {
        $acts = Act::query()
            ->active()
            ->select(['act_id', 'title', 'short_title', 'act_no', 'act_year', 'status'])
            ->orderBy('act_id')
            ->get();

        return response()->json($acts);
    }

    /**
     * GET /api/v1/acts/{act}/metadata
     * Get the AI-generated metadata for an act.
     * Returns 404 if metadata has not been generated yet.
     */
    public function show(Act $act): JsonResponse
    {
        $metadata = ActAiMetadata::where('act_id', $act->act_id)->first();

        if (! $metadata) {
            return response()->json(['message' => 'Metadata not found'], 404);
        }

        return response()->json($metadata);
    }

    /**
     * POST /api/v1/acts/{act}/metadata
     * Upsert AI-generated metadata for an act.
     * Called by legal-rag after LLM generation — not called by humans.
     */
    public function store(Request $request, Act $act): JsonResponse
    {
        $validated = $request->validate([
            'summary'              => 'nullable|string|max:1000',
            'keywords'             => 'nullable|array',
            'keywords.*'           => 'string|max:100',
            'legal_domains'        => 'nullable|array',
            'legal_domains.*'      => 'string|max:200',
            'regulated_activities' => 'nullable|array',
            'regulated_activities.*' => 'string|max:200',
            'subjects'             => 'nullable|array',
            'subjects.*'           => 'string|max:200',
        ]);

        $validated['act_id']       = $act->act_id;
        $validated['generated_at'] = now();

        $metadata = ActAiMetadata::updateOrCreate(
            ['act_id' => $act->act_id],
            $validated,
        );

        return response()->json($metadata, 201);
    }

    /**
     * GET /api/v1/acts/{act}/text-sample
     * Returns the title + first ~3 000 chars of leaf-node content for this act.
     * Used by legal-rag as the text input to LLM metadata generation.
     */
    public function textSample(Act $act): JsonResponse
    {
        $nodes = $act->legalNodes()
            ->where('has_children', false)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->select(['heading', 'text_content'])
            ->get();

        $parts = ["ACT: {$act->title}"];
        $total = 0;

        foreach ($nodes as $node) {
            $piece = trim(($node->heading ? $node->heading . "\n" : '') . ($node->text_content ?? ''));
            if (! $piece) {
                continue;
            }
            $total += strlen($piece);
            $parts[] = $piece;
            if ($total >= 3000) {
                break;
            }
        }

        return response()->json([
            'act_id'      => $act->act_id,
            'title'       => $act->title,
            'text_sample' => implode("\n\n", $parts),
        ]);
    }

    /**
     * GET /api/v1/acts/all-metadata
     * Return all acts that have AI metadata — used by legal-rag to build the act-level FAISS index.
     */
    public function allMetadata(): JsonResponse
    {
        $rows = ActAiMetadata::query()
            ->join('acts', 'acts.act_id', '=', 'act_ai_metadata.act_id')
            ->where('acts.status', 'active')
            ->select([
                'act_ai_metadata.id',
                'act_ai_metadata.act_id',
                'acts.title',
                'acts.short_title',
                'act_ai_metadata.summary',
                'act_ai_metadata.keywords',
                'act_ai_metadata.legal_domains',
                'act_ai_metadata.regulated_activities',
                'act_ai_metadata.subjects',
                'act_ai_metadata.generated_at',
            ])
            ->get();

        return response()->json($rows);
    }
}
