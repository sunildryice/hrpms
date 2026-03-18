<?php

namespace App\Http\Middleware;

use App\Repositories\ActivityLogRepository;
use Closure;
use Illuminate\Http\Request;

class ActivityLogger
{
    /**
     * @param ActivityLogRepository $activityLogs
     */
    public function __construct(
        ActivityLogRepository $activityLogs
    )
    {
        $this->activityLogs = $activityLogs;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->has('draw')) {
            $data = [
                'user_id' => auth()->id() ? auth()->id() : null,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'route' => $request->fullUrl(),
                'agent' => $request->server('HTTP_USER_AGENT'),
            ];
            $this->activityLogs->create($data);
        }
        if (auth()->check()) {
            $user = auth()->user()->fresh();
            if ($user->isLocked()) {
                auth()->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been locked. Please contact support.']);
            }
        }

        return $next($request);
    }
}
