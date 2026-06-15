<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalNode;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function search(Request $request): View
    {
        $results = collect();
        $query = $request->input('q', '');

        if (strlen($query) >= 3) {
            $results = LegalNode::with('act')
                ->where(function ($q) use ($query) {
                    $q->where('text_content', 'like', "%{$query}%")
                        ->orWhere('heading', 'like', "%{$query}%");
                })
                ->orderBy('act_id')
                ->orderBy('sort_order')
                ->limit(50)
                ->get()
                ->map(function (LegalNode $node) use ($query) {
                    $node->snippet = $this->extractSnippet($node->text_content ?? $node->heading ?? '', $query);
                    return $node;
                });
        }

        return view('admin.search.index', compact('results', 'query'));
    }

    private function extractSnippet(string $text, string $query): string
    {
        $pos = stripos($text, $query);
        if ($pos === false) {
            return mb_substr($text, 0, 200);
        }
        $start = max(0, $pos - 80);
        $snippet = mb_substr($text, $start, 250);
        return ($start > 0 ? '…' : '') . $snippet . (mb_strlen($text) > $start + 250 ? '…' : '');
    }
}
