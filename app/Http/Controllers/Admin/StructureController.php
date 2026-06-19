<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\LegalNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StructureController extends Controller
{
    public function index(): View
    {
        $acts = Act::withCount('legalNodes')
            ->orderByDesc('act_year')
            ->orderBy('act_no')
            ->get();

        return view('admin.structure.index', compact('acts'));
    }

    public function children(Request $request): JsonResponse
    {
        if ($request->filled('parent_node_id')) {
            $request->validate(['parent_node_id' => 'integer|exists:legal_nodes,node_id']);
            $nodes = LegalNode::where('parent_node_id', $request->integer('parent_node_id'))
                ->orderBy('sort_order')
                ->get(['node_id', 'node_type', 'node_no', 'heading', 'text_content', 'has_children', 'status', 'level']);
        } else {
            $request->validate(['act_id' => 'required|integer|exists:acts,act_id']);
            $nodes = LegalNode::where('act_id', $request->integer('act_id'))
                ->whereNull('parent_node_id')
                ->orderBy('sort_order')
                ->get(['node_id', 'node_type', 'node_no', 'heading', 'text_content', 'has_children', 'status', 'level']);
        }

        return response()->json($nodes);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);
        $q = $request->string('q')->trim();

        $nodes = LegalNode::with('act:act_id,act_no,act_year,title')
            ->where(function ($query) use ($q) {
                $query->where('heading', 'like', "%{$q}%")
                      ->orWhere('node_no', 'like', "%{$q}%")
                      ->orWhere('text_content', 'like', "%{$q}%");
            })
            ->orderBy('level')
            ->limit(60)
            ->get(['node_id', 'act_id', 'node_type', 'node_no', 'heading', 'text_content', 'status', 'level']);

        // Also search acts by title/no
        $actMatches = Act::where('title', 'like', "%{$q}%")
            ->orWhere('short_title', 'like', "%{$q}%")
            ->orWhere('act_no', 'like', "%{$q}%")
            ->limit(10)
            ->get(['act_id', 'act_no', 'act_year', 'title', 'status']);

        return response()->json([
            'acts'  => $actMatches,
            'nodes' => $nodes->map(fn ($n) => [
                'node_id'   => $n->node_id,
                'node_type' => $n->node_type,
                'node_no'   => $n->node_no,
                'heading'   => $n->heading,
                'preview'   => $n->text_content,
                'status'    => $n->status,
                'level'     => $n->level,
                'act_no'    => $n->act?->act_no,
                'act_year'  => $n->act?->act_year,
                'act_title' => $n->act?->title,
                'act_id'    => $n->act_id,
            ]),
        ]);
    }
}
