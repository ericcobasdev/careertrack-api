<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Requests\UpdateJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobApplication;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $jobApplications = JobApplication::latest()->get();

        return JobApplicationResource::collection($jobApplications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobApplicationRequest $request): JobApplicationResource
    {
        $jobApplication = $request->user()->jobApplications()->create(
            $request->validated()
        );

        return new JobApplicationResource($jobApplication);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobApplication $jobApplication): JobApplicationResource
    {
        return new JobApplicationResource($jobApplication);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobApplicationRequest $request, JobApplication $jobApplication): JobApplicationResource
    {
        $jobApplication->update($request->validated());

        return new JobApplicationResource($jobApplication);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobApplication $jobApplication): Response
    {
        $jobApplication->delete();

        return response()->noContent();
    }
}
