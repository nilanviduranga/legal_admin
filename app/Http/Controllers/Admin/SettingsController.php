<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\CaseLaw;
use App\Models\CrossReference;
use App\Models\Gazette;
use App\Models\LegalNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private function ragUrl(): string
    {
        return rtrim(config('app.rag_url', env('RAG_URL', 'http://127.0.0.1:8000')), '/');
    }

    private function postRag(string $path): array
    {
        $response = Http::timeout(600)->post($this->ragUrl() . $path);
        return ['status_code' => $response->status(), 'body' => $response->json()];
    }

    private function callRag(string $path): JsonResponse
    {
        try {
            $result = $this->postRag($path);
            return response()->json($result['body'], $result['status_code']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 502);
        }
    }

    public function index(): View
    {
        $tableCounts = [
            'gazettes'         => Gazette::count(),
            'acts'             => Act::count(),
            'legal_nodes'      => LegalNode::withTrashed()->count(),
            'soft_deleted'     => LegalNode::onlyTrashed()->count(),
            'cross_references' => CrossReference::count(),
            'case_laws'        => CaseLaw::count(),
        ];

        $dbStatus = 'connected';
        try {
            DB::connection()->getPdo();
        } catch (\Exception) {
            $dbStatus = 'error';
        }

        return view('admin.settings.index', compact('tableCounts', 'dbStatus'));
    }

    public function rebuildVectorDb(): JsonResponse
    {
        try {
            // Step 1: rebuild all FAISS indexes + act metadata
            $rebuild = $this->postRag('/vectordb/rebuild');
            if ($rebuild['status_code'] >= 400) {
                return response()->json($rebuild['body'], $rebuild['status_code']);
            }

            // Step 2: warm structure cache
            $warm = $this->postRag('/vectordb/warm-structure-cache');

            return response()->json([
                'status'          => 'full_rebuild_complete',
                'rebuild'         => $rebuild['body'],
                'structure_cache' => $warm['body'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 502);
        }
    }

    public function rebuildCaselaws(): JsonResponse
    {
        return $this->callRag('/vectordb/rebuild-caselaws');
    }

    public function rebuildActMetadata(): JsonResponse
    {
        return $this->callRag('/vectordb/rebuild-act-metadata');
    }

    public function warmStructureCache(): JsonResponse
    {
        return $this->callRag('/vectordb/warm-structure-cache');
    }
}
