<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Services\LegalDocumentParser;
use Illuminate\Http\JsonResponse;

class ActApiController extends Controller
{
    public function __construct(private readonly LegalDocumentParser $parser) {}

    public function tree(Act $act): JsonResponse
    {
        return response()->json($this->parser->buildTreeJson($act->act_id));
    }
}
