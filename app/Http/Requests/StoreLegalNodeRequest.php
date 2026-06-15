<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLegalNodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'act_id' => 'required|exists:acts,act_id',
            'parent_node_id' => 'nullable|exists:legal_nodes,node_id',
            'node_type' => 'required|in:PART,CHAPTER,SECTION,SUBSECTION,PARAGRAPH,SUBPARAGRAPH,ITEM,SCHEDULE,PREAMBLE,DEFINITIONS',
            'node_no' => 'nullable|string|max:20',
            'heading' => 'nullable|string|max:500',
            'text_content' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
            'level' => 'required|integer|min:1|max:10',
            'status' => 'sometimes|in:active,repealed,amended',
            'effective_date' => 'nullable|date',
            'repealed_date' => 'nullable|date',
            'version_no' => 'sometimes|integer|min:1',
        ];
    }
}
