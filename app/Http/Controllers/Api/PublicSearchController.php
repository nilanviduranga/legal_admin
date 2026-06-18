<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 3) {
            return response()->json(['query' => $query, 'results' => []]);
        }

        $results = LegalNode::query()
            ->with('act')
            ->where(function ($q) use ($query) {
                $q->where('text_content', 'like', "%{$query}%")
                    ->orWhere('heading', 'like', "%{$query}%");
            })
            ->orderBy('act_id')
            ->orderBy('sort_order')
            ->limit(50)
            ->get()
            ->map(fn (LegalNode $node) => [
                'node_id' => $node->node_id,
                'act_id' => $node->act_id,
                'act_title' => $node->act->title,
                'node_type' => $node->node_type,
                'node_no' => $node->node_no,
                'heading' => $node->heading,
                'full_path' => $node->getFullPath(),
                'snippet' => $this->extractSnippet($node->text_content ?? $node->heading ?? '', $query),
            ]);

        return response()->json(['query' => $query, 'results' => $results]);
    }

    private function extractSnippet(string $text, string $query): string
    {
        $pos = stripos($text, $query);
        if ($pos === false) {
            return mb_substr($text, 0, 200);
        }
        $start = max(0, $pos - 80);
        $snippet = mb_substr($text, $start, 250);

        return ($start > 0 ? '…' : '').$snippet.(mb_strlen($text) > $start + 250 ? '…' : '');
    }
}
