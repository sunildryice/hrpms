<?php

namespace Modules\Tracker\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDevelopment extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'business_developments';

    protected $fillable = [
        'thematic_area_id',
        'project_name',
        'date',
        'call_name',
        'funding_agency',
        'contracting_agency',
        'partnership_type',
        'consortium_lead',
        'consortium_partners',
        'project_type',
        'status',
        'result',
        'attachment',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function thematicArea()
    {
        return $this->belongsTo(ThematicArea::class, 'thematic_area_id')->withDefault();
    }

    public function getDate()
    {
        return $this->date?->toFormattedDateString();
    }

    public function getThematicAreaTitle()
    {
        return $this->thematicArea->title ?? '';
    }
}
