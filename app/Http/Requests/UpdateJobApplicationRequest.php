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
        $jobApplication = $this->route('jobApplication');
        $existingSalaryMin = $jobApplication?->salary_min;
        $existingAppliedAt = $jobApplication?->applied_at?->toDateString();

        return [
            'company_name' => ['sometimes', 'string', 'max:255'],
            'position_title' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', Rule::in(ApplicationStatus::values())],
            'source' => ['sometimes', 'nullable', 'string', 'max:255'],
            'source_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'salary_min' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'salary_max' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                Rule::when($this->filled('salary_min'), ['gte:salary_min']),
                Rule::when(! $this->has('salary_min') && $existingSalaryMin !== null, [
                    fn (string $attribute, mixed $value, \Closure $fail) => $value < $existingSalaryMin
                        ? $fail("The {$attribute} field must be greater than or equal to {$existingSalaryMin}.")
                        : null,
                ]),
            ],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'applied_at' => ['sometimes', 'nullable', 'date'],
            'next_step_at' => [
                'sometimes',
                'nullable',
                'date',
                Rule::when($this->has('applied_at'), ['after_or_equal:applied_at']),
                Rule::when(! $this->has('applied_at') && $existingAppliedAt, ["after_or_equal:{$existingAppliedAt}"]),
            ],
        ];
    }
}
