<?php

namespace Modules\Privilege\Repositories;

use App\Repositories\Repository;
use Modules\Privilege\Models\User;

class UserRepository extends Repository
{
    public function __construct(
        User $user,
        protected PermissionRepository $permissions
    ) {
        $this->model = $user;
    }

    public function getActiveUsers()
    {
        return $this->model->select(['*'])->with(['employee'])
            ->whereHas('employee')
            ->whereNotNull('activated_at')
            ->orderBy('full_name')->get();
    }

    public function getSupervisors($user)
    {
        $supervisors = collect();
        if ($user->employee) {
            $latestTenure = $user->employee->latestTenure;
            $supervisors = $this->model->select(['id', 'full_name'])
//                ->whereIn('employee_id', [$latestTenure->supervisor_id, $latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
                ->whereIn('employee_id', [$latestTenure->supervisor_id])
                ->whereHas('employee', function ($q) {
                    $q->select(['id']);
                    $q->whereNotNull('activated_at');
                })
                ->whereNotNull('activated_at')
                ->orderBy('full_name')
                ->get();
        }

        return $supervisors;
    }

    public function getSupervisor($user)
    {
        $supervisors = collect();
        if ($user->employee) {
            $latestTenure = $user->employee->latestTenure;
            $supervisors = $this->model->select(['id', 'full_name'])
                ->whereIn('employee_id', [$latestTenure->supervisor_id])
                ->whereHas('employee', function ($q) {
                    $q->select(['id']);
                    $q->whereNotNull('activated_at');
                })->whereNotNull('activated_at')->orderBy('full_name')->get();
        }

        return $supervisors;
    }

    public function permissionBasedUsers($guardName)
    {
        $permission = $this->permissions->findByField('guard_name', $guardName);
        if ($permission) {
            $authUser = auth()->user();
            $roles = $permission->roles->pluck('id');
            $query = $this->model->select(['*'])->whereNotNull('activated_at');

            $users = $query->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('id', $roles);
            })->whereDoesntHave('roles', function ($query) {
                $query->where('id', 1);
            })->orderby('full_name', 'asc')->get();

            return $users ? $users->reject(function ($user) use ($authUser) {
                return $user->id == $authUser->id;
            }) : collect();
        }

        return collect();
    }

    /**
     * Get all users with the specified guard names
     *
     * @param  string  $guardNames
     * @return \Illuminate\Support\Collection
     */
    public function multiPermissionBasedUsers(...$guardNames)
    {
        $roles = [];
        foreach ($guardNames as $guardName) {
            $permission = $this->permissions->findByField('guard_name', $guardName);
            if ($permission) {
                $authUser = auth()->user();
                $roles = array_merge($roles, $permission->roles->pluck('id')->toArray());
            }
        }
        if (count($roles)) {
            $query = $this->model->select(['*'])->whereNotNull('activated_at');

            $users = $query->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('id', $roles);
            })->whereDoesntHave('roles', function ($query) {
                $query->where('id', 1);
            })->orderby('full_name', 'asc')->get();

            return $users ? $users->reject(function ($user) use ($authUser) {
                return $user->id == $authUser->id;
            }) : collect();
        }

        return collect();
    }

    public function permissionBasedUsersInclusive($guardName)
    {
        $permission = $this->permissions->findByField('guard_name', $guardName);
        if ($permission) {
            $authUser = auth()->user();
            $roles = $permission->roles->pluck('id');
            $query = $this->model->select(['*'])->whereNotNull('activated_at');

            $users = $query->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('id', $roles);
            })->whereDoesntHave('roles', function ($query) {
                $query->where('id', 1);
            })->orderby('full_name', 'asc')->get();

            return $users;
        }

        return collect();
    }

    public function permissionBasedUsersByOfficeType($guardName, $officeIds)
    {
        $permission = $this->permissions->findByField('guard_name', $guardName);
        if ($permission) {
            $authUser = auth()->user();
            $roles = $permission->roles->pluck('id');
            $query = $this->model->select(['*'])->whereNotNull('activated_at');

            $users = $query->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('id', $roles);
            })->whereDoesntHave('roles', function ($query) {
                $query->where('id', 1);
            })->whereHas('employee.office', function ($query) use ($officeIds) {
                $query->whereIn('id', $officeIds);
            })->orderby('full_name', 'asc')->get();

            return $users ? $users->reject(function ($user) use ($authUser) {
                return $user->id == $authUser->id;
            }) : collect();
        }

        return collect();
    }
}
