<?php

namespace Modules\Memo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\FiscalYear;
use Modules\Memo\Models\MemoFrom;
use Modules\Memo\Models\MemoLogs;
use Modules\Memo\Models\MemoThrough;
use Modules\Memo\Models\MemoTo;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;


class Memo extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'memos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'office_id',
        'prefix',
        'memo_number',
        'modification_number',
        'modification_memo_id',
        'memo_date',
        'subject',
        'description',
        'enclosure',
        'attachment',
        'submitted_at',
        'status_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['memo_date', 'submitted_at'];

    /**
     * Get the approved log for the purchase request.
     */
    public function approvedLog()
    {
        return $this->hasOne(MemoLogs::class, 'memo_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function returnLog()
    {
        return $this->hasOne(MemoLogs::class, 'memo_id')
            ->whereStatusId(config('constant.RETURNED_STATUS'))
            ->latest();
    }


    /**
     * Get the createdBy
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the memo from.
     */
    public function from()
    {
        return $this->belongsToMany(User::class, 'memo_froms');
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the logs for the memo.
     */
    public function logs()
    {
        return $this->hasMany(MemoLogs::class, 'memo_id');
    }

    /**
     * Get the status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the memo through.
     */
    public function memoThrough()
    {
        return $this->belongsToMany(User::class, 'memo_throughs');
    }

    /**
     * Get the memo to.
     */
    public function to()
    {
        return $this->belongsToMany(User::class, 'memo_tos');
    }

    public function getCreatedBy()
    {
        return $this->createdBy->full_name;
    }

    public function getFrom()
    {
        if ($this->from) {
            foreach ($this->from as $staffs) {
                $staffs_name[] = $staffs->full_name;
            }
        }
        return implode(', ', $staffs_name);
    }

    public function getThrough()
    {
        $staffs_name = [];
        if ($this->through) {
            foreach ($this->through as $staffs) {
                $staffs_name[] = $staffs->full_name;
            }
        }
        return implode(', ', $staffs_name);
    }

    public function getThroughUserId()
    {
        $staffs_id = [];
        if ($this->through) {
            foreach ($this->through as $staffs) {
                $staffs_id[] = $staffs->id;
            }
        }
        return $staffs_id;
    }

    public function getTo()
    {
        if ($this->to) {
            foreach ($this->to as $staffs) {
                $staffs_name[] = $staffs->full_name;
            }
        }
        return implode(', ', $staffs_name);
    }

    public function getToUserId()
    {
        if ($this->to) {
            foreach ($this->to as $staffs) {
                $staffs_id[] = $staffs->id;
            }
        }
        return $staffs_id;
    }


    public function getMemoNumber()
    {
        $memoNumber = $this->prefix . '-' . $this->memo_number;
        $memoNumber .= $this->modification_number ? '-' . $this->modification_number : '';
        $fiscalYear = $this->fiscalYear ? '/' . substr($this->fiscalYear->title, 2) : '';
        return $memoNumber . $fiscalYear;
    }

    public function getMemoDate()
    {
        return $this->memo_date ? $this->memo_date->toFormattedDateString() : '';
    }

    public function getSubmittedDate()
    {
        return $this->submitted_at ? $this->submitted_at->toFormattedDateString() : '';
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function parentMemo()
    {
        return $this->belongsTo(Memo::class, 'modification_memo_id');
    }
}
