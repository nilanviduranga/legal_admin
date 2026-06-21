<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseLaw;
use Illuminate\Http\JsonResponse;

class CaseLawApiController extends Controller
{
    public function index(): JsonResponse
    {
        $cases = CaseLaw::active()
            ->orderByDesc('updated_at')
            ->get(['id', 'case_name', 'content']);

        return response()->json(
            $cases->map(fn (CaseLaw $c) => [
                'id'        => $c->id,
                'case_name' => $c->case_name,
                'content'   => $c->content,
            ])
        );
    }

    public function show(CaseLaw $caseLaw): JsonResponse
    {
        return response()->json([
            'id'        => $caseLaw->id,
            'case_name' => $caseLaw->case_name,
            'content'   => $caseLaw->content,
            'status'    => $caseLaw->status,
        ]);
    }
}
