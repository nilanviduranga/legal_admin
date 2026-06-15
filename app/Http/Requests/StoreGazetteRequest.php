<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGazetteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gazette_no' => 'required|string|max:50',
            'gazette_date' => 'required|date',
            'title' => 'required|string|max:500',
            'status' => 'sometimes|in:active,inactive',
        ];
    }
}
