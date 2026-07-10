<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;

class RiskHistory extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'risk_histories';

    protected $fillable = [
        'risk_id',
        'updated_date',
        'risk_status_id',
        'description_of_risk',
        'mitigating_action',
        'whats_changed_this_period',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'updated_date' => 'date',
    ];

    public function risk()
    {
        return $this->belongsTo(Risk::class);
    }

    public function riskStatus()
    {
        return $this->belongsTo(RiskStatus::class, 'risk_status_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getUpdatedDate()
    {
        return $this->updated_date?->toFormattedDateString();
    }

    public function getRiskStatusTitle()
    {
        return $this->riskStatus->title ?? '';
    }
}
