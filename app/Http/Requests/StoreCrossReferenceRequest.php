<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCrossReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_node_id' => 'required|exists:legal_nodes,node_id',
            'target_node_id' => 'required|exists:legal_nodes,node_id|different:source_node_id',
            'reference_text' => 'nullable|string|max:500',
            'reference_type' => 'required|in:refers_to,subject_to,as_amended_by,repeals,saves,cross_applies',
        ];
    }
}
