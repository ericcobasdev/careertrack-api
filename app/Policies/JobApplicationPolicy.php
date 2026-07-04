<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, JobApplication $jobApplication): bool
    {
        return $jobApplication->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, JobApplication $jobApplication): bool
    {
        return $jobApplication->user_id === $user->id;
    }

    public function delete(User $user, JobApplication $jobApplication): bool
    {
        return $jobApplication->user_id === $user->id;
    }

    public function restore(User $user, JobApplication $jobApplication): bool
    {
        return false;
    }

    public function forceDelete(User $user, JobApplication $jobApplication): bool
    {
        return false;
    }
}
