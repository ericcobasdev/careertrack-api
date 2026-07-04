<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexJobApplicationRequest;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Requests\UpdateJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class JobApplicationController extends Controller
{
    public function index(IndexJobApplicationRequest $request): AnonymousResourceCollection
    {
        $jobApplications = $request->user()
            ->jobApplications()
            ->filter($request->filters())
            ->orderBy($request->sortBy(), $request->sortDirection())
            ->paginate($request->perPage())
            ->withQueryString();

        return JobApplicationResource::collection($jobApplications);
    }

    public function store(StoreJobApplicationRequest $request): JsonResponse
    {
        $jobApplication = $request->user()->jobApplications()->create(
            $request->validated()
        );

        return (new JobApplicationResource($jobApplication))
            ->response()
            ->setStatusCode(201);
    }

    public function show(JobApplication $jobApplication): JobApplicationResource
    {
        $this->authorize('view', $jobApplication);

        return new JobApplicationResource($jobApplication);
    }

    public function update(UpdateJobApplicationRequest $request, JobApplication $jobApplication): JobApplicationResource
    {
        $jobApplication->update($request->validated());

        return new JobApplicationResource($jobApplication);
    }

    public function destroy(JobApplication $jobApplication): Response
    {
        $this->authorize('delete', $jobApplication);

        $jobApplication->delete();

        return response()->noContent();
    }
}
