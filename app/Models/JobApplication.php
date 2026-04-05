<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'position_title',
        'status',
        'source',
        'source_url',
        'salary_min',
        'salary_max',
        'location',
        'notes',
        'applied_at',
        'next_step_at',
    ];

    protected $casts = [
        'status' => ApplicationStatus::class,
        'applied_at' => 'date',
        'next_step_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['company'] ?? null, fn ($q, $company) => $q->where('company_name', 'like', "%{$company}%"))
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate('applied_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate('applied_at', '<=', $to));
    }
}