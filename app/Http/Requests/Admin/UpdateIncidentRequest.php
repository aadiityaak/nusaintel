<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-incidents') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'string', 'max:100'],
            'occurred_at' => ['nullable', 'date_format:Y-m-d'],
            'occurred_at_precision' => ['nullable', Rule::in(['exact', 'approximate', 'unknown'])],
            'published_at' => ['nullable', 'date_format:Y-m-d'],
            'reason' => ['required', 'string', 'max:2000'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'rejected'])],
            'confirm_publication' => ['sometimes', 'boolean'],
        ];
    }
}
