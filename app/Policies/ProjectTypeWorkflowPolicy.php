<?php

namespace App\Policies;

use App\Models\ProjectTypeWorkflow;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectTypeWorkflowPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_project') || $user->can('update_project');
    }

    public function view(User $user, ProjectTypeWorkflow $projectTypeWorkflow): bool
    {
        return $user->can('view_project') || $user->can('update_project');
    }

    public function create(User $user): bool
    {
        return $user->can('update_project');
    }

    public function update(User $user, ProjectTypeWorkflow $projectTypeWorkflow): bool
    {
        return $user->can('update_project');
    }

    public function delete(User $user, ProjectTypeWorkflow $projectTypeWorkflow): bool
    {
        return $user->can('update_project');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('update_project');
    }
}
