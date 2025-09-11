<?php

namespace Modules\EventCompletion\Models;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;

use Modules\Privilege\Models\User;
use Modules\Master\Models\District;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ProjectCode;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\ActivityCode;
use Modules\Attachment\Models\Attachment;
use Modules\EventCompletion\Models\EventParticipant;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class EventCompletion extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'event_completions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_id',
        'activity_code_id',
        'donor_code_id',
        'project_code_id',
        'account_code_id',
        'venue',
        'start_date',
        'end_date',
        'background',
        'objectives',
        'process',
        'closing',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'status_id',
        'created_by',
        'updated_by',
        'office_id'
     ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['start_date','end_date'];

    /**
     *  Get district name
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function getDistrictName(){
        return $this->district->district_name;
    }

    public function activityCode()
    {
       return $this->belongsTo(ActivityCode::class,'activity_code_id')->withDefault();
    }

    public function status(){
        return $this->belongsTo(Status::class,'status_id')->withDefault();
    }

    /**
     * Get the donor code
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

    public function projectCodes()
    {
        return $this->belongsTo(ProjectCode::class, 'project_code_id')->withDefault();
    }

    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    public function getStatusClass(){
        return $this->status->status_class;
    }

    public function getStatus(){
        return $this->status->title;
    }

    public function getActivityName(){
        return $this->activityCode->description;
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class,'event_completion_id')->orderBy('created_at');
    }

    public function getStartDate(){
        return $this->start_date?->format('M d, Y');
    }

    public function getEndDate(){
        return $this->end_date?->format('M d, Y');
    }

    public function getVenue(){
        return $this->venue;
    }

    /**
     * Get the logs for the travel request.
     */
    public function logs(){
        return $this->hasMany(EventLog::class,'event_completion_id')
        ->orderBy('created_at','desc');
    }

    /**
     * Get the submitted log for the local travel.
     */
    public function submittedLog()
    {
        return $this->hasOne(EventLog::class, 'event_completion_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * Get the approved log for the local travel.
     */
    public function approvedLog()
    {
        return $this->hasOne(EventLog::class, 'event_completion_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function returnedLog()
    {
        return $this->hasOne(EventLog::class, 'event_completion_id')
            ->where('status_id', config('constant.RETURNED_STATUS'))
            ->latest()->withDefault();
    }

    /**
     * Get the approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function getApproverName(){
        return $this->approver->getFullName();
    }

    public function getRequestDate()
    {
        return $this->request_date ? $this->request_date->toFormattedDateString() : '';
    }

    public function getReviewerName(){
        return $this->reviewer->getFullName();
    }

    public function attachments():MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the reviewer
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the office of the local travel
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the requester of the local travel
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    /**
     * Get the recommended log for the local travel request.
     */
    public function recommendedLog()
    {
        return $this->hasOne(LocalTravelLog::class, 'local_travel_reimbursement_id')
            ->whereStatusId(config('constant.RECOMMENDED_STATUS'))
            ->latest();
    }
   
    public function users(){
        return $this->belongsToMany(User::class,'event_participants', 'event_id','user_id');
    }

    public function getRequesterName(){
        return $this->requester->getFullName();
    }

    
   
}
