<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\ConstructionTrack\Models\ConstructionInstallment;
use Modules\Privilege\Models\User;

class TransactionType extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_transaction_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function constructionInstallments()
    {
        return $this->hasMany(ConstructionInstallment::class, 'transaction_type_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy?->getFullName();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy?->getFullName();
    }

    public function getCreatedAt()
    {
        return $this->created_at?->toFormattedDateString();
    }
    public function getUpdatedAt()
    {
        return $this->updated_at?->toFormattedDateString();
    }

}
