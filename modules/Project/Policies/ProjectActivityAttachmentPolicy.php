<?php

namespace Modules\Project\Policies;

use Modules\Privilege\Models\User;
use Modules\Project\Models\WorkPlan;
use Illuminate\Auth\Access\HandlesAuthorization;
use Carbon\Carbon;
use Modules\Project\Models\ProjectActivityAttachment;

class ProjectActivityAttachmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, ProjectActivityAttachment $projectActivityAttachment)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, ProjectActivityAttachment $projectActivityAttachment)
    {
        return true;
    }

    public function delete(User $user, ProjectActivityAttachment $projectActivityAttachment)
    {
        if ($user->id === $projectActivityAttachment->created_by) {
            return true;
        }
        return false;
    }
}
