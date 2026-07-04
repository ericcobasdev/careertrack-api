<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $jobApplication = $this->route('jobApplication');

        return $jobApplication
            ? $this->user()?->can('update', $jobApplication) ?? false
            : false;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['sometimes', 'string', 'max:255'],
            'position_title' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', Rule::in(ApplicationStatus::values())],
            'source' => ['sometimes', 'nullable', 'string', 'max:255'],
            'source_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'salary_min' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'salary_max' => ['sometimes', 'nullable', 'integer', 'min:0', Rule::when($this->filled('salary_min'), ['gte:salary_min'])],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'applied_at' => ['sometimes', 'nullable', 'date'],
            'next_step_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:applied_at'],
        ];
    }
}
