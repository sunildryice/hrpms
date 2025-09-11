<?php

namespace Modules\Master\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Master\Repositories\PackageRepository;
use Modules\Master\Requests\Package\StoreRequest;
use Modules\Master\Requests\Package\UpdateRequest;
use Modules\Privilege\Repositories\PermissionRepository;

class PackageController extends Controller
{
    protected $packages;
    /**
     * Create a new controller instance.
     *
     * @param PackageRepository $packages
     * @param PermissionRepository $permissions
     * @return void
     */
    public function __construct(
        PackageRepository $packages,
        PermissionRepository $permissions
    ) {
        $this->packages = $packages;
        $this->permissions = $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->packages->all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('item_count', function ($row) {
                    return $row->packageItems()->count();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->addColumn('action', function ($row) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('master.packages.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                $btn .= 'data-href="' . route('master.packages.destroy', $row->id) . '">';
                $btn .= '<i class="bi-trash"></i></a>';
                return $btn;
            })->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::PrPackage.index');
    }

    public function create()
    {
        return view('Master::PrPackage.create');
    }

    /**
     * Store a newly created purchase request pakcage in storage.
     *
     * @param \Modules\PurchaseRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['created_by'] = $authUser->id;
        $package = $this->packages->create($inputs);
        if ($package) {
            return redirect()->route('master.packages.edit', $package->id)
                ->withSuccessMessage('PR Package successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('PR Package cannot be added.');
    }

    public function show($id)
    {
        $authUser = auth()->user();
        $package = $this->packages->find($id);

        return view('Master::PrPackage.show')
            ->withPackage($package);
    }

    public function edit($id)
    {
        $authUser = auth()->user();
        $package = $this->packages->find($id);
        // $this->authorize('update', $package);

        return view('Master::PrPackage.edit')
            ->withAuthUser($authUser)
            ->withPackage($package);
    }

    public function update(UpdateRequest $request, $id)
    {

        $package = $this->packages->find($id);
        // $this->authorize('update', $package);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $package = $this->packages->update($id, $inputs);
        if ($package) {
            $message = 'PR Package is successfully updated.';
            if ($request->ajax()) {
                return response()->json([
                    'type' => 'success',
                    'message' => $message,
                ], 200);
            }
            return redirect()->back()->withInput()
                ->withSuccessMessage($message);
        }
        if ($request->ajax()) {
            return response()->json([
                'type' => 'error',
                'message' => 'PR Package can not be updated.',
            ], 422);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('PR Package can not be updated.');
    }

    public function destroy($id)
    {

        $package = $this->packages->find($id);
        // $this->authorize('delete', $package);
        $flag = $this->packages->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'PR Package is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'PR Package can not deleted.',
        ], 422);
    }

}
