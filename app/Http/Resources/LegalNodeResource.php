<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LegalNodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'node_id' => $this->node_id,
            'act_id' => $this->act_id,
            'parent_node_id' => $this->parent_node_id,
            'node_type' => $this->node_type,
            'node_no' => $this->node_no,
            'heading' => $this->heading,
            'text_content' => $this->text_content,
            'level' => $this->level,
            'sort_order' => $this->sort_order,
            'has_children' => $this->has_children,
            'status' => $this->status,
            'effective_date' => $this->effective_date?->toDateString(),
            'repealed_date' => $this->repealed_date?->toDateString(),
            'full_path' => $this->getFullPath(),
        ];
    }
}
