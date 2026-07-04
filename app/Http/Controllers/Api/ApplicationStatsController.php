<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationStatsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', JobApplication::class);

        $baseQuery = $request->user()->jobApplications();
        $byStatus = collect(ApplicationStatus::cases())
            ->mapWithKeys(fn (ApplicationStatus $status) => [
                $status->value => (clone $baseQuery)->where('status', $status->value)->count(),
            ]);

        return response()->json([
            'data' => [
                'total' => (clone $baseQuery)->count(),
                'by_status' => $byStatus,
                'upcoming_next_steps' => (clone $baseQuery)
                    ->whereNotNull('next_step_at')
                    ->where('next_step_at', '>=', now())
                    ->count(),
            ],
        ]);
    }
}
