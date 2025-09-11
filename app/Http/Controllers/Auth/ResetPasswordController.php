<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Http\Request;
use Modules\Privilege\Repositories\UserRepository;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use CanResetPassword;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $users
    )
    {
        $this->middleware(['guest']);
        $this->users = $users;
    }

    /**
     * Display a form to create password
     *
     * @param $token
     * @return mixed
     */
    public function create($token)
    {
        $user = $this->users->findByField('reset_token', $token);
        if ($user) {
            return view('auth.reset_password')
                ->withUser($user);
        } else {
            return redirect()->route('signin')
                ->withWarningMessage(config('app.name') . ' could not locate the information needed to recover your password. Please try again.');
        }
    }

    /**
     * Store user password in storage
     *
     * @param Request $request
     * @param $token
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, $token)
    {
        $this->validate($request, [
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);
        $user = $this->users->findByField('reset_token', $token);
        $inputs = ['password' => bcrypt($request->new_password), 'activated_at'=>date('Y-m-d H:i:s'), 'reset_token'=>NULL];
        $this->users->update($user->id, $inputs);
        if ($user) {
            return redirect()->route('signin')
                ->withSuccessMessage('You have successfully reset your password.');
        } else {
            return redirect()->back()
                ->withWarningMessage('Server Error! Please try again.');
        }
    }
}
