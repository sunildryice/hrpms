<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\AuditLogRepository;
use Modules\Privilege\Repositories\UserDelegationRepository;
use Modules\Privilege\Repositories\UserRepository;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $loginPath = '/login';

    protected $redirectPath = '/dashboard';

    protected $redirectTo = '/dashboard';

    /**
     * The Log repository implementation.
     *
     * @var AuditLogRepository
     */
    protected $logs;

    /**
     * The User repository implementation.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Create a new controller instance.
     *
     * @param AuditLogRepository $logs
     * @param UserRepository $users
     * @param UserDelegationRepository $userDelegations
     * @return void
     */
    public function __construct(
        AuditLogRepository            $logs,
        UserRepository           $users,
        UserDelegationRepository $userDelegations
    )
    {
        $this->logs = $logs;
        $this->users = $users;
        $this->userDelegations = $userDelegations;
        $this->middleware('guest')->except(['logout', 'loginas', 'loginasOriginal']);
    }

    /**
     * Login Page.
     *
     */
    public function login(Request $request)
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        $request->session()->flush();
        $remember = $request->has('remember');
        if (Auth::attempt(['email_address' => $request->username, 'password' => $request->password], $remember)) {
            $user = auth()->user();
            if($user->activated_at) {
                self::afterLogin($user);
                // if ($request->has('previous')) {
                //     return redirect()->intended($request->previous);
                // }
                return redirect()->intended(route('dashboard.index'));
            } else {
                auth()->logout();
                return redirect()->route('login')
                    ->withWarningMessage('You can not login now.<br /> You are not activated. Please contact your system administrator.');
            }
        }
        return redirect()->route('login')
            ->withInput()
            ->withWarningMessage('Invalid username or password.');
    }

    /**
     * Handle an authentication logout.
     *
     */
    public function logout(Request $request)
    {
        $this->logs->create([
            'user_id' => auth()->id(),
            'action' => 'Logout',
            'description' => "Log out from " . env('APP_NAME'),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ]);
        Auth::logout();
        $request->session()->flush();
        return redirect()->route('login');
    }

    public function loginas($id)
    {
        $delegation = $this->userDelegations->find($id);
        if ($delegation->start_date <= date('Y-m-d') && $delegation->end_date >= date('Y-m-d') && $delegation->to_user == auth()->id() && !session()->exists('original_user')) {
            $loginUser = Auth::loginUsingId($delegation->from_user);
            $this->logs->create([
                'user_id' => $loginUser->id,
                'action' => 'Login',
                'description' => "Logged in to " . env('APP_NAME') . ' by ' . $delegation->toUser->full_name,
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);
            session()->put('original_user', $delegation->to_user);
            self::afterLogin($loginUser);
            return redirect()->intended(route('dashboard'));
        }
        return redirect()->back()
            ->withWarningMessage('you are denied.');
    }

    public function loginasOriginal()
    {
        if (session()->exists('original_user')) {
            $loginUser = Auth::loginUsingId(session()->get('original_user'));
            $this->logs->create([
                'user_id' => $loginUser->id,
                'action' => 'Login',
                'description' => "Logged in to " . env('APP_NAME') . ' by ' . $loginUser->full_name,
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);
            session()->forget('original_user');
            self::afterLogin($loginUser);
            return redirect()->intended(route('dashboard'));
        }
        return redirect()->back()
            ->withWarningMessage('you are denied.');
    }

    public function afterLogin($user)
    {
        $permissions = collect();
        $roles = collect();
        foreach($user->roles as $role){
            $permissions->push($role->permissions);
            $roles->push($role->id);
        }
        $access_permissions = [];
        foreach($permissions->flatten(1) as $index=>$permission){
            $access_permissions[] = $permission->guard_name;
        }
        $access_permissions = array_unique($access_permissions);
        session()->put('access_permissions', $access_permissions);
        session()->put('roles', $user->roles);
        return true;
    }
}
