<?php

namespace Modules\Mfr\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Attachment\Models\Attachment;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class Transaction extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'mfr_transactions';

    protected $fillable = [
        'mfr_agreement_id',
        'requester_id',
        'reviewer_id',
        'verifier_id',
        'recommender_id',
        'approver_id',
        'month',
        'transaction_type',
        'transaction_date',
        'approved_amount',
        'approved_budget',
        'release_amount',
        'expense_amount',
        'reimbursed_amount',
        'remarks',
        'question_remarks',
        'status_id',
        'created_by',
        'updated_by',
    ];

    /*
 * Work Flow
 *
 * created: 1
 * submitted: 2
 * verified: 11
 * verified2: 14
 * recommend: 4
 * approved : 6
 * */

    protected $hidden = [];

    protected $dates = ['transaction_date'];

    // protected $casts = [
    //     'checkin'   => 'datetime',
    //     'checkout'  => 'datetime'
    // ];
    //

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'mfr_agreement_id')->withDefault();
    }

    public function logs()
    {
        return $this->hasmany(TransactionLog::class, 'mfr_transaction_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id')->withDefault();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    public function getRequester()
    {
        return $this->requester->getFullName();
    }

    public function getReviewer()
    {
        return $this->reviewer->getFullName();
    }

    public function getVerifier()
    {
        return $this->verifier->getFullName();
    }

    public function getRecommender()
    {
        return $this->recommender->getFullName();
    }

    public function getType()
    {
        return $this->transaction_type == '1' ? 'Fund Release' : 'Fund Release/Mfr';
    }

    public function getApprover()
    {
        return $this->approver->getFullName();
    }

    public function getQuestionedCost()
    {
        return $this->expense_amount - $this->reimbursed_amount;
    }

    public function getPOName()
    {
        return $this->agreement->getPOName();
    }

    public function getApprovedAmount()
    {
        return $this->approved_amount;
    }

    public function getApprovedBudget()
    {
        return $this->approved_budget;
    }
}
