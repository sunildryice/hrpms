<?php

namespace Modules\ConstructionTrack\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ConstructionTrack\Models\ConstructionParty;
use Modules\Privilege\Models\User;

class ConstructionPartyPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        // 
    }

    public function deletable(User $user, ConstructionParty $constructionParty)
    {
        return $constructionParty->deletable == 0 ? false : true;
    }
}