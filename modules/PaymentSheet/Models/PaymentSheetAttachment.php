<?php

namespace Modules\PaymentSheet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;
use Modules\PaymentSheet\Models\PaymentSheet;

class PaymentSheetAttachment extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_sheet_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_sheet_id',
        'title', 
        'attachment',
        'link',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    public function paymentSheet()
    {
        return $this->belongsTo(PaymentSheet::class, 'payment_sheet_id')->withDefault();
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getTitle()
    {
        return ucfirst($this->title);
    }

    public function getAttachment()
    {
        return $this->attachment;
    }


}