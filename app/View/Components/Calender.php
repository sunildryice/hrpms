<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;

class Calender extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(protected LeaveRequestRepository $leaveRequest) {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $today = Carbon::now();

        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth   = $today->copy()->endOfMonth();


        return view('components.calender', [
            'leaveRequests' => $this->leaveRequest,
        ]);
    }
}
