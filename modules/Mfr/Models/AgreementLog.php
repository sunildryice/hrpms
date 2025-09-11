<?php
namespace Modules\Mfr\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class AgreementLog extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'mfr_agreement_logs';

    protected $fillable = [
        'mfr_agreement_id',
        'user_id',
        'original_user_id',
        'log_remarks',
        'status_id',
    ];

    protected $hidden = [];

    protected $dates = ['created_at', 'updated_at'];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'mfr_agreement_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function originalUser()
    {
        return $this->belongsTo(User::class, 'original_user_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

}
