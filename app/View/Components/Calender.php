<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;
use Modules\Privilege\Models\User;

class Calender extends Component
{


    public function render()
    {
        $office = User::find(auth()->id())->employee->office;

        return view('components.calender', [
            'office' => $office,
        ]);
    }
}
