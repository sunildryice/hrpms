<?php

namespace Modules\Privilege\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Privilege\Repositories\UserDelegationRepository;
use Modules\Privilege\Repositories\UserRepository;

use Image;

class UserProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  UserRepository $users
     * @param  UserDelegationRepository $userDelegations
     * @return void
     */
    public function __construct(
        UserRepository $users,
        UserDelegationRepository $userDelegations
    )
    {
        $this->users = $users;
        $this->userDelegations = $userDelegations;
        $this->destinationPath = 'users/';
        $this->thumb_width = 300;
        $this->thumb_height = 300;
        $this->thumb_extension = '.jpg';
    }

    /**
     * Display a listing of the delegation.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // If summary tab is active
        $activeTab = 'overview';

        $param = $request->all();
        if(isset($param['tab']))
        {
            $activeTab = $param['tab'];
        }

        $user = auth()->user();
        $this->authorize('view-profile');
        $user = $this->users->with([
            'leaves.leaveType','leaves.fiscalYear'
        ])->find($user->id);
        $delegations = $this->userDelegations->with(['fromUser','toUser'])
            ->where('from_user', '=', $user->id)
            ->orWhere('to_user', $user->id)
            ->orderby('start_date', 'desc')
            ->get();

        $userLogs = $user->logs()->orderby('id', 'desc')->take(20)->get();

        return view('Privilege::User.profile')
            ->withUser($user)
            ->withUserLogs($userLogs)
            ->withOriginalUser(session()->get('original_user'))
            ->withActiveTab($activeTab)
            ->withDelegations($delegations);
    }


    /**
     * Update the own profile in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $this->authorize('view-profile');
        if ($request->file('signature')) {
            $data['signature'] = $request->file('signature')->storeAs($this->destinationPath.$user->id, time().'.'. $request->file('signature')->getClientOriginalExtension());
            if (file_exists($this->destinationPath . $user->signature) && $user->signature != '') {
                unlink($this->destinationPath . $user->signature);
            }
            $user->update($data);

            return response()->json(['status' => 'ok',
                'user' => $user,
                'message' => 'Profile is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'user' => $user,
            'message' => 'Profile is not updated.'], 422);
    }
}


