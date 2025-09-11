<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\Privilege\Models\User;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'audit_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'original_user_id',
        'model',
        'model_id',
        'action',
        'description',
        'before_details',
        'after_details',
        'ip_address'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    /*
     * Get user of the log
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->withDefault();
    }

    /*
     * Get original user of the log
     */
    public function originalUser()
    {
        return $this->belongsTo(User::class, 'original_user_id');
    }

    public function getUserFullNameAndEmail()
    {
        return $this->user ? $this->user->full_name .' ('. $this->user->email_address .')' : '';
    }

    public function getUserEmail()
    {
        return $this->user ? $this->user->email_address : '';
    }

    public function getUserFullName()
    {
        return $this->user ? $this->user->full_name : '';
    }

    public function getOriginalUserFullNameAndEmail()
    {
        return $this->originalUser ? $this->originalUser->full_name .' ('. $this->originalUser->email_address .')' : '';
    }

    public function getCreatedAt()
    {
        return $this->created_at->format('M d, Y h:i A');
    }
}
