<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'act_id' => $this->act_id,
            'act_no' => $this->act_no,
            'act_year' => $this->act_year,
            'title' => $this->title,
            'short_title' => $this->short_title,
            'commencement_date' => $this->commencement_date?->toDateString(),
            'status' => $this->status,
            'current_version' => $this->current_version,
            'replaces_act_id' => $this->replaces_act_id,
            'replaced_by_act_id' => $this->replaced_by_act_id,
            'gazette' => new GazetteResource($this->whenLoaded('gazette')),
        ];
    }
}
