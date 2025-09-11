<?php

namespace Modules\Mfr\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Attachment\Models\Attachment;
use Modules\Master\Models\District;
use Modules\Master\Models\Office;
use Modules\Master\Models\PartnerOrganization;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class Agreement extends Model
{
    use HasFactory, ModelEventLogger;

    // Database table used by the model.
    protected $table = 'mfr_agreements';

    // Attributes that are mass assignable.
    protected $fillable = [
        'partner_organization_id',
        'district_id',
        'project_id',
        'grant_number',
        'effective_from',
        'effective_to',
        'approved_budget',
        'opening_balance',
        'opening_remarks',
        'created_by',
        'updated_by',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'status_id',
    ];

    protected $casts = ['effective_to' => 'date:Y-m-d'];

    // Attributes hidden from models JSON or array.
    protected $hidden = [];

    // Turn the columns into carbon object.
    protected $dates = ['effective_from', 'effective_to', 'created_at', 'updated_at'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'mfr_agreement_id');
    }

    public function partnerOrganization()
    {
        return $this->belongsTo(PartnerOrganization::class, 'partner_organization_id')->withDefault();
    }

    public function amendments()
    {
        return $this->hasMany(AgreementAmendment::class, 'mfr_agreement_id');
    }

    public function latestAmendment()
    {
        return $this->hasOne(AgreementAmendment::class, 'mfr_agreement_id')->latestOfMany()->withDefault();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function project()
    {
        return $this->belongsTo(ProjectCode::class, 'project_id')->withDefault();
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function dutyStation()
    {
        return $this->employee->latestTenure->dutyStation;
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function logs()
    {
        return $this->hasMany(AgreementLog::class, 'mfr_agreement_id');
    }

    public function latestLog()
    {
        return $this->hasOne(AgreementLog::class, 'mfr_agreement_id')->withDefault()->latest();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getMonth()
    {
        return date('F', mktime(0, 0, 0, $this->month, 10));

    }

    public function getYearMonth()
    {
        return $this->getMonth().', '.$this->getYear();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getEffectiveFrom()
    {
        return $this->effective_from;
    }

    public function getEffectiveTo()
    {
        if ($this->latestAmendment()->exists() && $this->latestAmendment->effective_date <= now()) {
            return $this->latestAmendment->extension_to_date;
        }

        return $this->effective_to;
    }

    public function getEffectiveToDate()
    {
        return $this->getEffectiveTo()->format('Y-m-d');
    }

    public function getEffectiveFromDate()
    {
        return $this->getEffectiveFrom()->format('Y-m-d');
    }

    public function getApprovedBudget()
    {
        if ($this->latestAmendment()->exists() && $this->latestAmendment->effective_date <= now()) {
            return $this->latestAmendment->approved_budget;
        }

        return $this->approved_budget;
    }

    public function getPOName()
    {
        return $this->partnerOrganization->name;
    }
}
