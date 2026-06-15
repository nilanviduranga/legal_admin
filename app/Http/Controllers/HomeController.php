<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Act;
use App\Models\LegalNode;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'actsCount' => Act::query()->count(),
            'sectionsCount' => LegalNode::query()->count(),
            'currentSectionsCount' => LegalNode::query()->where('status', 'active')->count(),
        ]);
    }
}
