<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Act;
use App\Models\CrossReference;
use App\Models\Gazette;
use App\Models\LegalNode;
use Illuminate\Support\Facades\DB;

class LegalDocumentParser
{
    /**
     * Import from parsed JSON structure (from AI or manual).
     * $data = { gazette: {...}, act: {...}, nodes: [{...children:[...]}], cross_references: [...] }
     */
    public function importFromJson(array $data): Act
    {
        return DB::transaction(function () use ($data) {
            $gazette = Gazette::firstOrCreate(
                ['gazette_no' => $data['gazette']['gazette_no']],
                [
                    'gazette_date' => $data['gazette']['gazette_date'],
                    'title' => $data['gazette']['title'],
                    'status' => 'active',
                ]
            );

            $act = Act::firstOrCreate(
                ['act_no' => $data['act']['act_no'], 'act_year' => $data['act']['act_year']],
                [
                    'gazette_id' => $gazette->gazette_id,
                    'title' => $data['act']['title'],
                    'short_title' => $data['act']['short_title'] ?? null,
                    'commencement_date' => $data['act']['commencement_date'] ?? null,
                    'status' => 'active',
                    'current_version' => true,
                ]
            );

            // Delete existing nodes if re-importing
            LegalNode::withTrashed()->where('act_id', $act->act_id)->forceDelete();

            foreach ($data['nodes'] as $nodeData) {
                $this->insertNode($nodeData, $act->act_id, null);
            }

            if (!empty($data['cross_references'])) {
                $this->insertCrossReferences($data['cross_references'], $act->act_id);
            }

            return $act->fresh();
        });
    }

    private function insertNode(array $nodeData, int $actId, ?int $parentId): LegalNode
    {
        $node = LegalNode::create([
            'act_id' => $actId,
            'parent_node_id' => $parentId,
            'node_type' => $nodeData['node_type'],
            'node_no' => $nodeData['node_no'] ?? null,
            'heading' => $nodeData['heading'] ?? null,
            'text_content' => $nodeData['text_content'] ?? null,
            'sort_order' => $nodeData['sort_order'] ?? 0,
            'level' => $nodeData['level'],
            'has_children' => !empty($nodeData['children']),
            'status' => 'active',
            'version_no' => 1,
        ]);

        if (!empty($nodeData['children'])) {
            foreach ($nodeData['children'] as $child) {
                $this->insertNode($child, $actId, $node->node_id);
            }
        }

        return $node;
    }

    private function insertCrossReferences(array $refs, int $actId): void
    {
        foreach ($refs as $ref) {
            $source = $this->resolveNodeByPath($ref['source_node_path'], $actId);
            $target = $this->resolveNodeByPath($ref['target_node_path'], $actId);

            if ($source && $target && $source->node_id !== $target->node_id) {
                CrossReference::firstOrCreate(
                    ['source_node_id' => $source->node_id, 'target_node_id' => $target->node_id],
                    [
                        'reference_text' => $ref['reference_text'] ?? null,
                        'reference_type' => $ref['reference_type'] ?? 'refers_to',
                    ]
                );
            }
        }
    }

    private function resolveNodeByPath(string $path, int $actId): ?LegalNode
    {
        // path format: "73.(1)" or "20.(4)" or just "73" or "Schedule"
        $parts = explode('.', trim($path));
        $sectionNo = trim($parts[0]);
        $subsectionNo = isset($parts[1]) ? trim($parts[1]) : null;

        $query = LegalNode::where('act_id', $actId)
            ->where('node_no', $sectionNo);

        if ($subsectionNo) {
            $section = $query->whereIn('node_type', ['SECTION', 'SCHEDULE', 'PART'])->first();
            if (!$section) {
                return null;
            }
            return LegalNode::where('act_id', $actId)
                ->where('parent_node_id', $section->node_id)
                ->where('node_no', $subsectionNo)
                ->first();
        }

        return $query->first();
    }

    /**
     * Build full nested tree array for an act (for preview / API).
     */
    public function buildTreeJson(int $actId): array
    {
        $roots = LegalNode::where('act_id', $actId)
            ->whereNull('parent_node_id')
            ->orderBy('sort_order')
            ->with('allChildren')
            ->get();

        return $roots->map(fn ($n) => $this->nodeToArray($n))->toArray();
    }

    private function nodeToArray(LegalNode $node): array
    {
        return [
            'node_id' => $node->node_id,
            'node_type' => $node->node_type,
            'node_no' => $node->node_no,
            'heading' => $node->heading,
            'text_content' => $node->text_content,
            'level' => $node->level,
            'sort_order' => $node->sort_order,
            'has_children' => $node->has_children,
            'status' => $node->status,
            'children' => $node->allChildren->map(fn ($c) => $this->nodeToArray($c))->toArray(),
        ];
    }
}
