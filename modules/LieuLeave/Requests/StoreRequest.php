<?php

namespace Modules\LieuLeave\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Modules\LieuLeave\Repositories\LieuLeaveBalanceRepository;

class StoreRequest extends FormRequest
{
    protected $lieuLeaveBalance;

    public function __construct(LieuLeaveBalanceRepository $lieuLeaveBalance)
    {
        parent::__construct();
        $this->lieuLeaveBalance = $lieuLeaveBalance;
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'leave_date'    => 'required|date',
            'off_day_work_date' => 'required|date',
            'reason'        => 'required|string',
            'send_to'       => 'required|exists:users,id',
            'substitutes'   => 'nullable|array',
            'substitutes.*' => 'exists:employees,id',
            'btn'           => 'required|string',
        ];
    }
}
