<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

use Modules\Privilege\Repositories\UserRepository;

class HeaderComposer
{
    /**
     * Create a new header composer.
     *
     * @param UserRepository $users
     * @return void
     */
    public function __construct(
        UserRepository $users
    ){
        $this->users = $users;
    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $authUser = auth()->user();
        $view->withUser($authUser)
            ->withNotificationCount($authUser->unreadNotifications()->whereDate('created_at', '>', now()->subDays(8))->count())
            ->withNotifications($authUser->unreadNotifications->sortByDesc('created_at')->take(3));

        return $view;
    }
}
