<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLegalNodeRequest;
use App\Models\Act;
use App\Models\LegalNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LegalNodeController extends Controller
{
    public function byAct(Request $request): JsonResponse
    {
        $request->validate(['act_id' => 'required|exists:acts,act_id']);

        $nodes = LegalNode::where('act_id', $request->integer('act_id'))
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get(['node_id', 'node_type', 'node_no', 'heading', 'level'])
            ->map(fn ($n) => [
                'node_id' => $n->node_id,
                'label'   => str_repeat('  ', $n->level - 1)
                             . $n->node_type
                             . ($n->node_no ? ' ' . $n->node_no : '')
                             . ($n->heading ? ' — ' . \Str::limit($n->heading, 60) : ''),
            ]);

        return response()->json($nodes);
    }

    public function create(Request $request): View
    {
        $act = Act::findOrFail($request->query('act_id'));
        $parent = $request->query('parent_node_id')
            ? LegalNode::findOrFail($request->query('parent_node_id'))
            : null;

        $nextSortOrder = LegalNode::where('act_id', $act->act_id)
            ->where('parent_node_id', $parent?->node_id)
            ->max('sort_order') + 1;

        return view('admin.nodes.create', compact('act', 'parent', 'nextSortOrder'));
    }

    public function index(Act $act): View
    {
        $nodes = $act->topLevelNodes()->with('allChildren')->get();
        return view('admin.nodes.index', compact('act', 'nodes'));
    }

    public function show(LegalNode $node): View
    {
        $node->load([
            'act',
            'parent',
            'children',
            'replacedByNode',
            'replacesNode',
            'outgoingReferences.targetNode',
            'incomingReferences.sourceNode',
        ]);
        return view('admin.nodes.show', compact('node'));
    }

    public function edit(LegalNode $node): View
    {
        $node->load(['replacedByNode']);
        return view('admin.nodes.edit', compact('node'));
    }

    public function store(StoreLegalNodeRequest $request): RedirectResponse
    {
        $node = DB::transaction(function () use ($request) {
            $node = LegalNode::create($request->validated());

            if ($node->parent_node_id) {
                LegalNode::where('node_id', $node->parent_node_id)
                    ->update(['has_children' => true]);
            }

            return $node;
        });

        return redirect()->route('admin.acts.nodes.index', $node->act_id)
            ->with('success', 'Node created.');
    }

    public function update(StoreLegalNodeRequest $request, LegalNode $node): RedirectResponse
    {
        DB::transaction(function () use ($request, $node) {
            $oldParent = $node->parent_node_id;
            $node->update($request->validated());
            $newParent = $node->parent_node_id;

            if ($oldParent && $oldParent !== $newParent) {
                $hasChildren = LegalNode::where('parent_node_id', $oldParent)->exists();
                LegalNode::where('node_id', $oldParent)->update(['has_children' => $hasChildren]);
            }
            if ($newParent) {
                LegalNode::where('node_id', $newParent)->update(['has_children' => true]);
            }
        });

        return redirect()->route('admin.nodes.show', $node)->with('success', 'Node updated.');
    }

    public function newVersion(LegalNode $node): RedirectResponse
    {
        $newNode = DB::transaction(function () use ($node) {
            $newNode = LegalNode::create([
                'act_id'          => $node->act_id,
                'parent_node_id'  => $node->parent_node_id,
                'node_type'       => $node->node_type,
                'node_no'         => $node->node_no,
                'heading'         => $node->heading,
                'text_content'    => $node->text_content,
                'sort_order'      => $node->sort_order,
                'level'           => $node->level,
                'has_children'    => false,
                'status'          => 'active',
                'version_no'      => $node->version_no + 1,
                'replaces_node_id' => $node->node_id,
            ]);

            $node->update([
                'replaced_by_node_id' => $newNode->node_id,
                'status'              => 'amended',
            ]);

            return $newNode;
        });

        return redirect()->route('admin.nodes.edit', $newNode)
            ->with('success', 'New version created — edit the content below.');
    }

    public function destroy(LegalNode $node): RedirectResponse
    {
        $actId = $node->act_id;
        $parentId = $node->parent_node_id;

        DB::transaction(function () use ($node, $parentId) {
            $node->delete();

            if ($parentId) {
                $hasChildren = LegalNode::where('parent_node_id', $parentId)->exists();
                LegalNode::where('node_id', $parentId)->update(['has_children' => $hasChildren]);
            }
        });

        return redirect()->route('admin.acts.show', $actId)->with('success', 'Node deleted.');
    }
}
