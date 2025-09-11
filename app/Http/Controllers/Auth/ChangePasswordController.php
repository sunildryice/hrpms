<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Modules\Privilege\Repositories\UserRepository;

class ChangePasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Change Password Controller
    |--------------------------------------------------------------------------
    */

    /**
     * Create a new controller instance.
     *
     * @param UserRepository $users
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'web']);
    }

    public function create()
    {
        return view('auth.change_password');
    }

    public function store(ChangePasswordRequest $request)
    {
        $user = auth()->user();
        $user->password = bcrypt($request->new_password);
        $user->save();
        auth()->logout();
        return redirect()->route('signin')
            ->withSuccessMessage('Password successfully updated.');
    }

}
