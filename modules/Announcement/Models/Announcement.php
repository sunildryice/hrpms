<?php

namespace Modules\Announcement\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\FiscalYear;
use Modules\Privilege\Models\User;

class Announcement extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'description',
        'attachment',
        'fiscal_year_id',
        'prefix',
        'announcement_number',
        'published_date',
        'expiry_date',
        'extended_date',
        'created_by'
    ];

    protected $dates = [
        'published_date',
        'expiry_date',
        'extended_date'
    ];

    protected $hidden = [];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function getAnnouncementNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2): '';
        return $this->prefix.'-'.$this->announcement_number . $fiscalYear;
    }

    public function getTitle()
    {
        return $this->title;
        // return ucfirst($this->title);
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPublishedDate()
    {
        return $this->published_date?->toFormattedDateString();
    }

    public function getExpiryDate()
    {
        return $this->expiry_date?->toFormattedDateString();
    }

    public function getExtendedDate()
    {
        return $this->extended_date?->toFormattedDateString();
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->getFiscalYear();
    }

    public function getCreatorName()
    {
        return $this->createdBy->getFullName();
    }

}
