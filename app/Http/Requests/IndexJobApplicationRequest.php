<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', JobApplication::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', Rule::in(ApplicationStatus::values())],
            'company' => ['sometimes', 'string', 'max:255'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date', 'after_or_equal:from'],
            'sort_by' => ['sometimes', 'string', Rule::in(['applied_at', 'created_at', 'updated_at', 'company_name', 'status'])],
            'sort_direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function filters(): array
    {
        return $this->safe()->only(['status', 'company', 'from', 'to']);
    }

    public function sortBy(): string
    {
        return $this->validated('sort_by', 'created_at');
    }

    public function sortDirection(): string
    {
        return $this->validated('sort_direction', 'desc');
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', 15);
    }
}
