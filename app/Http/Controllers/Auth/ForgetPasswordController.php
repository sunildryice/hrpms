<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Modules\Privilege\Repositories\UserRepository;

class ForgetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @param UserRepository $users
     * @return void
     */
    public function __construct(
        UserRepository $users
    )
    {
        $this->users = $users;
    }

    public function create()
    {
        return view('auth.forget_password');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email_address' => [
                'required', 'email',
                Rule::exists('users'),
            ],
        ]);
        $user = $this->users->findByField('email_address', $request->email_address);
        if($user->employee->activated_at) {
            $user->update(['reset_token' => \Str::random(60)]);
            if ($user) {
                Mail::to($user->email_address)
                    ->send(new ForgetPassword($user));
                return redirect()->route('signin')
                    ->withSuccessMessage('Please check your email to reset your password.');
            }
            return redirect()->route('signin')
                ->withWarningMessage('Token can not be reset.');
        } else {
            return redirect()->back()
                ->withWarningMessage('Employee profile is deactivated.');
        }
    }

}
