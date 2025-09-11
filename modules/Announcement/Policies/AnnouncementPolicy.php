<?php

namespace Modules\Announcement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Announcement\Models\Announcement;
use Modules\Privilege\Models\User;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Announcement\Models\Announcement  $announcement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Announcement\Models\Announcement  $announcement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Announcement\Models\Announcement  $announcement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Announcement\Models\Announcement  $announcement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Announcement $announcement)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Announcement\Models\Announcement  $announcement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Announcement $announcement)
    {
        //
    }
}
