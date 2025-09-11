<?php

namespace Modules\Privilege\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;

use Modules\Configuration\Repositories\DepartmentRepository;
use Modules\Configuration\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

class UserExportController extends Controller
{
    /**
     * UserExportController constructor.
     *
     * @param DepartmentRepository $departments
     * @param OfficeRepository $offices
     * @param RoleRepository $roles
     * @param UserRepository $users
     */
    public function __construct(
        DepartmentRepository $departments,
        OfficeRepository $offices,
        RoleRepository $roles,
        UserRepository $users
    ){
        $this->departments = $departments;
        $this->offices = $offices;
        $this->roles = $roles;
        $this->users = $users;
    }


    /**
     * Store supplier data from spreadsheet
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $users = $this->users->get();

        $data = [];

        foreach ($users as $index => $record) {
            $data[] = [
                'SN' => $index + 1,
                'Name' => $record->full_name,
                'Email Address' => $record->email_address,
                'Phone Number' => $record->phone_number,
                'Designation' => $record->designation,
                'Office' => $record->getOfficeName(),
                'Roles' => implode(',',$record->roles()->pluck('role')->toArray()),
                'Department' => $record->department->name,
                'Employee Code' => $record->employee_code
            ];
        }

        Excel::create('Users', function ($excel) use ($data) {
            $excel->setTitle('Users');
            $excel->setCreator(config('app.name'))
                ->setCompany(config('app.name'));
            $excel->sheet('Items', function ($sheet) use ($data) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($data);
            });
        })->export('xls');
    }

}
