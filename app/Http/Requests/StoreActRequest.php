<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gazette_id' => 'required|exists:gazettes,gazette_id',
            'act_no' => 'required|integer|min:1',
            'act_year' => 'required|integer|digits:4',
            'title' => 'required|string|max:500',
            'short_title' => 'nullable|string|max:200',
            'commencement_date' => 'nullable|date',
            'status' => 'sometimes|in:active,repealed,amended',
            'replaced_by_act_id' => 'nullable|exists:acts,act_id',
            'replaces_act_id' => 'nullable|exists:acts,act_id',
        ];
    }
}
