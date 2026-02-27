<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;
use Modules\Project\Models\Project;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Department;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\TravelType;
use Modules\Master\Models\ProjectCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelRequestView extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'view_travel_requests';


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['departure_date', 'return_date'];

    public function getDepartureDate()
    {
        return $this->departure_date ? $this->departure_date->toFormattedDateString() : '';
    }

    public function getReturnDate()
    {
        return $this->return_date ? $this->return_date->toFormattedDateString() : '';
    }

    /**
     * Get the total days of the travel.
     */
    public function getTotalDays()
    {
        return $this->return_date ? $this->return_date->diffInDays($this->departure_date) + 1 : 1;
    }

    public function getTravelRequestNumber()
    {
        $travelNumber = $this->prefix . '-' . $this->travel_number;
        $travelNumber .= $this->modification_number ? '-' . $this->modification_number : '';
        $fiscalYear = '/' . substr($this->fiscal_year, 2);

        return $travelNumber . $fiscalYear;
    }
}
