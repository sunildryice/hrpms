<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Office;

use Modules\Master\Models\DonorCode;

use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\ActivityCode;
use Modules\Project\Models\ProjectActivity;
use Modules\TravelRequest\Models\TravelRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelClaimExpense extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_claim_expenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_claim_id',
        'activity_code_id',
        'donor_code_id',
        'expense_date',
        'expense_description',
        'expense_amount',
        'invoice_bill_number',
        'office_id',
        'attachment',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['expense_date'];

    /**
     * Get the travel claim of the travel expenses.
     */
    public function travelClaim()
    {
        return $this->belongsTo(TravelClaim::class, 'travel_claim_id');
    }

    /**
     * Get the activityCode of the travel claim expense.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    public function activity()
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_code_id')->withDefault();
    }

    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    /**
     * Get the charging office
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getExpenseDate()
    {
        return $this->expense_date ? $this->expense_date->toFormattedDateString() : '';
    }

    public function getActivityTitle()
    {
        return $this->activityCode->title;
    }

    public function getDonorDescription()
    {
        return $this->donorCode->description;
    }

    public function getAmount()
    {
        return $this->expense_amount;
    }
}
