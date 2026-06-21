<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\CaseLaw;
use App\Models\CrossReference;
use App\Models\Gazette;
use App\Models\LegalNode;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingsController extends Controller
{
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
}
