<?php

namespace Modules\ConstructionTrack\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ConstructionTrack\Models\ConstructionProgress;
use Modules\Privilege\Models\User;

class ConstructionProgressPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        // 
    }

    public function add(User $user, ConstructionProgress $constructionProgress)
    {
        return $user->can('addProgress', $constructionProgress->construction);
    }

    public function edit(User $user, ConstructionProgress $constructionProgress)
    {
        return $user->can('addProgress', $constructionProgress->construction);
    }

    public function delete(User $user, ConstructionProgress $constructionProgress)
    {
        return $user->can('addProgress', $constructionProgress->construction);
    }

    public function manageAttachment(User $user, ConstructionProgress $constructionProgress)
    {
        return $user->can('addProgress', $constructionProgress->construction);
    }
}