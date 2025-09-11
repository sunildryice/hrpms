<?php
namespace Modules\Privilege\Repositories;

use App\Repositories\Repository;
use Modules\Privilege\Models\Role;

use DB;

class RoleRepository extends Repository
{
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $role = $this->model->create($inputs);
            $role->permissions()->sync($inputs['permissions']);
            DB::commit();
            return $role;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $role = $this->model->findOrFail($id);
            $role->fill($inputs)->save();
            $role->permissions()->detach();
            if(array_key_exists('permissions', $inputs)){
                $role->permissions()->sync($inputs['permissions']);
            }
            DB::commit();
            return $role;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
