<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\CaseLaw;
use App\Models\CrossReference;
use App\Models\Gazette;
use App\Models\LegalNode;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'gazettes'   => Gazette::count(),
            'acts'       => Act::count(),
            'nodes'      => LegalNode::count(),
            'references' => CrossReference::count(),
            'case_laws'  => CaseLaw::count(),
        ];

        $actsByStatus = Act::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $nodesByType = LegalNode::selectRaw('node_type, COUNT(*) as count')
            ->groupBy('node_type')
            ->orderByDesc('count')
            ->get();

        $recentActs = Act::with('gazette')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'actsByStatus', 'nodesByType', 'recentActs'));
    }
}
