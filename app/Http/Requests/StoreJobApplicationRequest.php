<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', JobApplication::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'position_title' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(ApplicationStatus::values())],
            'source' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0', Rule::when($this->filled('salary_min'), ['gte:salary_min'])],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'applied_at' => ['nullable', 'date'],
            'next_step_at' => ['nullable', 'date', 'after_or_equal:applied_at'],
        ];
    }
}
