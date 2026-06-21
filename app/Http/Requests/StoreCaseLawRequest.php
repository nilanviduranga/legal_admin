<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCaseLawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'case_name' => ['required', 'string', 'max:500'],
            'content'   => ['required', 'string'],
            'status'    => ['required', 'in:active,overruled'],
        ];
    }
}
