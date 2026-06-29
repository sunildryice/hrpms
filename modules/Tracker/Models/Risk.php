<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\Project\Models\Project;

class Risk extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'risks';

    protected $fillable = [
        'project',
        'project_id',
        'date_added',
        'risk_name',
        'risk_status_id',
        'risk_type_id',
        'risk_probability_id',
        'risk_impact_id',
        'risk_rating_id',
        'risk_response_type_id',
        'description_of_risk',
        'risk_owner',
        'mitigating_action',
        'whats_changed_this_quarter',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_added' => 'date',
    ];

    public function riskStatus()
    {
        return $this->belongsTo(RiskStatus::class, 'risk_status_id')->withDefault();
    }

    public function riskType()
    {
        return $this->belongsTo(RiskType::class, 'risk_type_id')->withDefault();
    }

    public function riskProbability()
    {
        return $this->belongsTo(RiskProbability::class, 'risk_probability_id')->withDefault();
    }

    public function riskImpact()
    {
        return $this->belongsTo(RiskImpact::class, 'risk_impact_id')->withDefault();
    }

    public function riskRating()
    {
        return $this->belongsTo(RiskRating::class, 'risk_rating_id')->withDefault();
    }

    public function riskResponseType()
    {
        return $this->belongsTo(RiskResponseType::class, 'risk_response_type_id')->withDefault();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getDateAdded()
    {
        return $this->date_added?->toFormattedDateString();
    }

    public function getRiskStatusTitle()
    {
        return $this->riskStatus->title ?? '';
    }

    public function getRiskTypeTitle()
    {
        return $this->riskType->title ?? '';
    }

    public function getRiskProbabilityTitle()
    {
        return $this->riskProbability->title ?? '';
    }

    public function getRiskImpactTitle()
    {
        return $this->riskImpact->title ?? '';
    }

    public function getRiskRatingTitle()
    {
        return $this->riskRating->title ?? '';
    }

    public function getRiskResponseTypeTitle()
    {
        return $this->riskResponseType->title ?? '';
    }

    public function getProjectTitle()
    {
        return $this->project?->short_name ?? $this->project?->title ?? 'N/A';
    }

    public function getCreatorName()
    {
        return $this->createdBy->getFullName();
    }
}
