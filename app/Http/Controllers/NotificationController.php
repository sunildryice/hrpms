<?php

namespace App\Http\Controllers;

use App\Repositories\NotificationRepository;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Session;
use Modules\Privilege\Repositories\UserRepository;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UserRepository $users
     * @return void
     */
    public function __construct(
        NotificationRepository $notifications,
        UserRepository         $users
    ) {
        $this->notifications = $notifications;
        $this->users = $users;
    }

    /**
     * Display a listing of the notifications.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $this->users->find(auth()->id());
        if ($request->ajax()) {
            return response()->json([
                'unreadNotificationCount' => $user->unreadNotifications->count(),
                'notifications' => $user->notifications->take(3),
            ], 200);
        }
        //        $user->unreadNotifications->markAsRead();

        return view('notifications.index')
            ->withNotifications($user->notifications()->whereDate('created_at', '>', now()->subDays(8))->get());
    }

    public function show(Request $request, $id)
    {
        $notification = $this->notifications->find($id);
        $jsonData = json_decode($notification->data);
        $route = $alternateRoute = $jsonData->link;
        if (property_exists($jsonData, 'alternate_link')) {
            $alternateRoute = $jsonData->alternate_link;
        }
        $notification->update(['read_at' => date('Y-m-d H:i:s')]);
        return redirect($route);

        try {
            $name = Session::getName();
            $sessionId = $_COOKIE[$name];

            $cookieJar = CookieJar::fromArray([
                $name => $sessionId,
            ], env('APP_DOMAIN', 'hrpms.dryicesolutions.net'));

            $client = new Client([
                'headers' => [
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36',
                    'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'
                ]
            ]);
            $client->request('GET', $route, ['cookies' => $cookieJar]);
            return redirect($route);
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            return redirect($alternateRoute);
        }
    }
}
