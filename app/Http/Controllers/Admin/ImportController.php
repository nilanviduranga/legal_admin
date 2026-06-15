<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LegalDocumentParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function __construct(private readonly LegalDocumentParser $parser) {}

    public function showImportForm(): View
    {
        return view('admin.import.index');
    }

    public function processImport(Request $request): JsonResponse
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $pdfPath = $request->file('pdf_file')->store('imports', 'local');
        $fullPath = storage_path('app/' . $pdfPath);

        // Extract text from PDF using pdftotext if available, otherwise read raw
        $text = $this->extractPdfText($fullPath);

        $parsed = $this->callClaudeApi($text);

        return response()->json(['preview' => $parsed]);
    }

    public function confirmImport(Request $request): RedirectResponse
    {
        $request->validate(['data' => 'required|array']);

        $act = $this->parser->importFromJson($request->input('data'));

        return redirect()->route('admin.acts.show', $act)
            ->with('success', 'Act imported successfully.');
    }

    private function extractPdfText(string $path): string
    {
        if (function_exists('exec')) {
            $escaped = escapeshellarg($path);
            $output = shell_exec("pdftotext {$escaped} - 2>/dev/null");
            if ($output) {
                return $output;
            }
        }
        return file_get_contents($path) ?: '';
    }

    private function callClaudeApi(string $text): array
    {
        $apiKey = config('services.anthropic.key');

        $systemPrompt = <<<'PROMPT'
You are a legal document parser. Extract the structure of the uploaded Sri Lankan Act into strict JSON.

Return ONLY valid JSON, no explanation, no markdown. Structure:

{
  "gazette": { "gazette_no": "string", "gazette_date": "YYYY-MM-DD", "title": "string" },
  "act": { "act_no": 9, "act_year": 2003, "title": "string", "short_title": "string or null", "commencement_date": "YYYY-MM-DD or null", "replaces_acts": [] },
  "nodes": [{ "node_type": "PART|SECTION|SUBSECTION|PARAGRAPH|SUBPARAGRAPH|ITEM|SCHEDULE|PREAMBLE", "node_no": "I or 2 or (1) or (a)", "heading": "string or null", "text_content": "string or null", "level": 1, "sort_order": 1, "children": [...] }],
  "cross_references": [{ "source_node_path": "73.(1)", "target_node_path": "12", "reference_text": "string", "reference_type": "refers_to|subject_to|as_amended_by|repeals|saves|cross_applies" }]
}

Rules:
- PART nodes: node_no is Roman numeral (I, II, III...), heading is the PART title
- SECTION nodes: node_no is integer string, heading is margin title
- SUBSECTION nodes: node_no is "(1)", "(2)"...
- PARAGRAPH nodes: node_no is "(a)", "(b)"...
- SUBPARAGRAPH nodes: node_no is "(i)", "(ii)"...
- ITEM nodes: node_no is "(aa)", "(bb)"...
- text_content should be actual text of leaf nodes; parent nodes have null if all text is in children
- Include SCHEDULE as top-level node and PREAMBLE if present
- For cross_references: scan every text_content for patterns like "section X", "under section", "mutatis mutandis"
- reference_type: "repeals" if "hereby repealed"; "saves" if "shall not apply" or "notwithstanding"; "cross_applies" if "mutatis mutandis"; "subject_to" if "subject to section"; else "refers_to"
PROMPT;

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-opus-4-8',
            'max_tokens' => 8000,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => "Parse this Sri Lankan Act:\n\n" . mb_substr($text, 0, 50000)],
            ],
        ]);

        $content = $response->json('content.0.text', '{}');
        return json_decode($content, true) ?? [];
    }
}
