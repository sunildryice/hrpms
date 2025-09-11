<?php

namespace Modules\Privilege\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'permission_name',
        'guard_name',
        'activated_at',
        'hierarchy',
        'stage',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];

    /**
     * Get parent permission of the child permission
     */
    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id')->withDefault();
    }

    /**
     * Get childrens permission of the permission
     */
    public function childrens()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }

    /**
     * Get all roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }

    public function getActive()
    {
        return $this->activated_at ? 'Yes' : 'No';
    }

    public function getParent()
    {
        return $this->parent->permission_name;
    }
}
