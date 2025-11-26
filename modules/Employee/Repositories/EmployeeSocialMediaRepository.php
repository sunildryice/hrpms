<?php

namespace Modules\Employee\Repositories;


use App\Repositories\Repository;
use Modules\Employee\Models\SocialMedia as EmployeeSocialMedia;

class EmployeeSocialMediaRepository extends Repository
{

    public function __construct(EmployeeSocialMedia $socialMedia)
    {
        $this->model = $socialMedia;
    }

    public function getSocialMediaLinksByEmployeeId($employeeId)
    {
        $table = $this->model->getTable();

        return $this->model
            ->from($table . ' as esa')
            ->join('lkup_social_accounts as lsa', 'esa.social_account_id', '=', 'lsa.id')
            ->select(['esa.*', 'lsa.title'])
            ->where('esa.employee_id', $employeeId)
            ->get();
    }
}
