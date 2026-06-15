<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCrossReferenceRequest;
use App\Models\Act;
use App\Models\CrossReference;
use App\Models\LegalNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CrossReferenceController extends Controller
{
    public function create(Request $request): View
    {
        $acts = Act::orderBy('act_year')->orderBy('act_no')
            ->get(['act_id', 'act_no', 'act_year', 'short_title', 'title']);

        $sourceNode = $request->filled('source_node_id')
            ? LegalNode::findOrFail($request->integer('source_node_id'))
            : null;

        return view('admin.cross-references.create', compact('acts', 'sourceNode'));
    }

    public function edit(CrossReference $crossReference): View
    {
        $crossReference->load(['sourceNode.act', 'targetNode.act']);

        $acts = Act::orderBy('act_year')->orderBy('act_no')
            ->get(['act_id', 'act_no', 'act_year', 'short_title', 'title']);

        return view('admin.cross-references.edit', compact('crossReference', 'acts'));
    }

    public function allIndex(Request $request): View
    {
        $query = CrossReference::with([
            'sourceNode.act',
            'targetNode.act',
        ]);

        if ($actId = $request->integer('act_id')) {
            $query->where(function ($q) use ($actId) {
                $q->whereHas('sourceNode', fn ($s) => $s->where('act_id', $actId))
                  ->orWhereHas('targetNode', fn ($t) => $t->where('act_id', $actId));
            });
        }

        if ($type = $request->get('type')) {
            $query->where('reference_type', $type);
        }

        $references = $query->latest()->paginate(30)->withQueryString();

        $acts = Act::orderBy('act_year')->orderBy('act_no')->get(['act_id', 'act_no', 'act_year', 'title']);

        $typeCounts = CrossReference::selectRaw('reference_type, COUNT(*) as count')
            ->groupBy('reference_type')
            ->pluck('count', 'reference_type')
            ->toArray();

        $totalCount = CrossReference::count();

        return view('admin.cross-references.index', compact('references', 'acts', 'typeCounts', 'totalCount'));
    }

    public function index(Request $request): JsonResponse
    {
        $nodeId = $request->integer('node_id');

        $outgoing = CrossReference::where('source_node_id', $nodeId)
            ->with('targetNode')
            ->get();

        $incoming = CrossReference::where('target_node_id', $nodeId)
            ->with('sourceNode')
            ->get();

        return response()->json(compact('outgoing', 'incoming'));
    }

    public function store(StoreCrossReferenceRequest $request): RedirectResponse
    {
        DB::transaction(fn () => CrossReference::create($request->validated()));

        if ($request->filled('redirect_node_id')) {
            return redirect()
                ->route('admin.nodes.show', $request->integer('redirect_node_id'))
                ->with('success', 'Cross-reference added.');
        }

        return redirect()
            ->route('admin.cross-references.index')
            ->with('success', 'Cross-reference added.');
    }

    public function update(StoreCrossReferenceRequest $request, CrossReference $crossReference): RedirectResponse
    {
        DB::transaction(fn () => $crossReference->update($request->validated()));

        return redirect()
            ->route('admin.cross-references.index')
            ->with('success', 'Cross-reference updated.');
    }

    public function destroy(CrossReference $crossReference): RedirectResponse
    {
        DB::transaction(fn () => $crossReference->delete());
        return redirect()->back()->with('success', 'Cross-reference removed.');
    }
}
