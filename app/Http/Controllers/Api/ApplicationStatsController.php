<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;

class ApplicationStatsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'total' => JobApplication::count(),
            'applied' => JobApplication::where('status', 'applied')->count(),
            'interview' => JobApplication::where('status', 'interview')->count(),
            'offer' => JobApplication::where('status', 'offer')->count(),
            'rejected' => JobApplication::where('status', 'rejected')->count(),
        ]);
    }
}
