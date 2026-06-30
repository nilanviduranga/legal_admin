<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\ActStatistic;
use App\Models\LegalNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActStructureApiController extends Controller
{
    /**
     * GET /api/v1/acts/search?q=Consumer+Affairs
     * Find acts by title or short_title — used by legal-rag to resolve act names from user queries.
     * Returns up to 5 best matches ordered by title relevance.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json(['error' => 'Query parameter q is required'], 422);
        }

        $acts = Act::query()
            ->where(function ($query) use ($q): void {
                $query->where('title', 'LIKE', "%{$q}%")
                    ->orWhere('short_title', 'LIKE', "%{$q}%");
            })
            ->select(['act_id', 'title', 'short_title', 'act_no', 'act_year', 'status'])
            ->orderByRaw("CASE WHEN title LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy('title')
            ->limit(5)
            ->get();

        return response()->json($acts);
    }

    /**
     * GET /api/v1/acts/{act}/structure-stats
     * Returns per-type node counts for an act.
     * Uses act_statistics cache — recomputes if stale or missing.
     * Response shape mirrors StructuralResult used by legal-rag.
     */
    public function stats(Act $act): JsonResponse
    {
        $cached = ActStatistic::where('act_id', $act->act_id)->first();

        if (! $cached) {
            $cached = $this->computeAndCache($act);
        }

        $typeCounts = LegalNode::query()
            ->where('act_id', $act->act_id)
            ->select('node_type', DB::raw('COUNT(*) as count'))
            ->groupBy('node_type')
            ->pluck('count', 'node_type')
            ->toArray();

        return response()->json([
            'type'             => 'STRUCTURAL_RESULT',
            'act_id'           => $act->act_id,
            'act'              => $act->title,
            'short_title'      => $act->short_title,
            'sections_count'   => $cached->sections_count,
            'subsections_count' => $cached->subsections_count,
            'parts_count'      => $cached->parts_count,
            'chapters_count'   => $cached->chapters_count,
            'paragraphs_count' => $cached->paragraphs_count,
            'items_count'      => $cached->items_count,
            'total_nodes_count' => $cached->total_nodes_count,
            'counts_by_type'   => $typeCounts,
            'computed_at'      => $cached->computed_at,
        ]);
    }

    /**
     * GET /api/v1/acts/{act}/nodes-by-type?node_type=SECTION
     * Returns an ordered list of nodes of the given type within the act.
     * Defaults to SECTION if node_type is omitted.
     */
    public function listByType(Act $act, Request $request): JsonResponse
    {
        $nodeType = strtoupper(trim((string) $request->query('node_type', 'SECTION')));

        $validTypes = ['PART', 'CHAPTER', 'SECTION', 'SUBSECTION', 'PARAGRAPH', 'SUBPARAGRAPH', 'ITEM', 'SCHEDULE', 'PREAMBLE', 'DEFINITIONS'];

        if (! in_array($nodeType, $validTypes, true)) {
            return response()->json(['error' => "Invalid node_type. Allowed: " . implode(', ', $validTypes)], 422);
        }

        $nodes = LegalNode::query()
            ->where('act_id', $act->act_id)
            ->where('node_type', $nodeType)
            ->select(['node_id', 'node_no', 'heading', 'sort_order', 'level', 'status'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'type'      => 'STRUCTURAL_LIST',
            'act_id'    => $act->act_id,
            'act'       => $act->title,
            'node_type' => $nodeType,
            'count'     => $nodes->count(),
            'nodes'     => $nodes,
        ]);
    }

    /**
     * POST /api/v1/acts/{act}/statistics/refresh
     * Force-recompute and cache structure statistics for the act.
     * Called by legal-rag after vectordb rebuild to warm the cache.
     */
    public function refreshStats(Act $act): JsonResponse
    {
        $cached = $this->computeAndCache($act);

        return response()->json([
            'act_id'      => $act->act_id,
            'act'         => $act->title,
            'computed_at' => $cached->computed_at,
            'stats'       => [
                'sections'    => $cached->sections_count,
                'subsections' => $cached->subsections_count,
                'parts'       => $cached->parts_count,
                'chapters'    => $cached->chapters_count,
                'paragraphs'  => $cached->paragraphs_count,
                'items'       => $cached->items_count,
                'total'       => $cached->total_nodes_count,
            ],
        ]);
    }

    // ── Private helpers ─────────────────────────────────────────────────────

    private function computeAndCache(Act $act): ActStatistic
    {
        $counts = LegalNode::query()
            ->where('act_id', $act->act_id)
            ->select('node_type', DB::raw('COUNT(*) as count'))
            ->groupBy('node_type')
            ->pluck('count', 'node_type')
            ->toArray();

        $data = [
            'act_id'            => $act->act_id,
            'sections_count'    => (int) ($counts['SECTION']    ?? 0),
            'subsections_count' => (int) ($counts['SUBSECTION'] ?? 0),
            'parts_count'       => (int) ($counts['PART']       ?? 0),
            'chapters_count'    => (int) ($counts['CHAPTER']    ?? 0),
            'paragraphs_count'  => (int) ($counts['PARAGRAPH']  ?? 0),
            'items_count'       => (int) ($counts['ITEM']        ?? 0),
            'total_nodes_count' => (int) array_sum($counts),
            'computed_at'       => now(),
        ];

        return ActStatistic::updateOrCreate(['act_id' => $act->act_id], $data);
    }
}
