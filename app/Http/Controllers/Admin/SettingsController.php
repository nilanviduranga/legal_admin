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
        return rtrim(env('RAG_URL', 'http://127.0.0.1:8000'), '/');
    }

    private function callRag(string $path): JsonResponse
    {
        try {
            $response = Http::timeout(600)->post($this->ragUrl() . $path);
            return response()->json($response->json(), $response->status());
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

    public function rebuildStatutes(): JsonResponse
    {
        return $this->callRag('/vectordb/rebuild-statutes');
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
