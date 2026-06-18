<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GazetteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'gazette_id' => $this->gazette_id,
            'gazette_no' => $this->gazette_no,
            'gazette_date' => $this->gazette_date?->toDateString(),
            'title' => $this->title,
            'status' => $this->status,
            'acts' => ActResource::collection($this->whenLoaded('acts')),
        ];
    }
}
