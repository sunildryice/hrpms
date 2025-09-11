<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Imports\ActivityCodesImport;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;

use DataTables;
use Excel;

class ActivityCodeImportController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param AccountCodeRepository $accountCodes
     * @param ActivityCodeRepository $activityCodes
     * @return void
     */
    public function __construct(
        AccountCodeRepository $accountCodes,
        ActivityCodeRepository $activityCodes
    )
    {
        $this->accountCodes = $accountCodes;
        $this->activityCodes = $activityCodes;
    }

    /**
     * Show the form for creating a new activity code.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $accountCodes = $this->accountCodes->select(['id', 'title'])->whereNotNull('activated_at')->get();
        return view('Master::ActivityCode.import')
            ->withAccountCodes($accountCodes);
    }

    /**
     * Store a newly created activity code in storage.
     *
     * @param \Modules\Master\Requests\ActivityCode\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        Excel::import(new ActivityCodesImport(), request()->file('attachment'));
       
        return back()->withSuccessMessage('Activity codes are imported successfully.');

    }
}
