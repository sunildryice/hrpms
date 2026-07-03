<?php

namespace Modules\Tracker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Tracker\Models\ThematicArea;
use Modules\Tracker\Repositories\BusinessDevelopmentRepository;
use Modules\Tracker\Requests\BusinessDevelopment\StoreRequest;
use Modules\Tracker\Requests\BusinessDevelopment\UpdateRequest;
use Yajra\DataTables\DataTables;

class BusinessDevelopmentController extends Controller
{
    public function __construct(
        BusinessDevelopmentRepository $businessDevelopments,
    )
    {
        $this->businessDevelopments = $businessDevelopments;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $this->authorize('manage-business-development');

        if ($request->ajax()) {
            $data = $this->businessDevelopments->with(['thematicArea'])
                ->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('thematic_area', function ($row) {
                    return $row->getThematicAreaTitle();
                })
                ->addColumn('project_name', function ($row) {
                    return $row->project_name ?: 'N/A';
                })
                ->addColumn('date', function ($row) {
                    return $row->getDate();
                })
                ->addColumn('funding_agency', function ($row) {
                    return $row->funding_agency ?: 'N/A';
                })
                ->addColumn('partnership_type', function ($row) {
                    return $row->partnership_type ?: 'N/A';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('business-development.show', $row->id) . '" rel="tooltip" title="View"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('manage-business-development')) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('business-development.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }

                    if ($authUser->can('manage-business-development')) {
                        $btn .= '&emsp;<a href="javascript:;" class="btn btn-danger btn-sm delete-record" rel="tooltip" title="Delete" ';
                        $btn .= 'data-href="' . route('business-development.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Tracker::BusinessDevelopment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage-business-development');

        $thematicAreas = ThematicArea::all();

        return view('Tracker::BusinessDevelopment.create', compact('thematicAreas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('manage-business-development');

        $inputs = $request->validated();

        if ($request->file('attachment')) {
            $inputs['attachment'] = $request->file('attachment')
                ->storeAs('tracker/business-development', time().'_'.random_int(1000, 9999).'_attachment.'.$request->file('attachment')->getClientOriginalExtension());
        }

        $record = $this->businessDevelopments->create($inputs);

        if ($record) {
            return redirect()->route('business-development.index')->withSuccessMessage('Business Development created successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Business Development could not be created.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $businessDevelopment = $this->businessDevelopments->find($id);
        return view('Tracker::BusinessDevelopment.show', compact('businessDevelopment'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('manage-business-development');

        $businessDevelopment = $this->businessDevelopments->find($id);
        $thematicAreas       = ThematicArea::all();

        return view('Tracker::BusinessDevelopment.edit', compact('businessDevelopment', 'thematicAreas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $this->authorize('manage-business-development');

        $inputs = $request->validated();

        if ($request->file('attachment')) {
            $businessDevelopment = $this->businessDevelopments->find($id);
            if ($businessDevelopment->attachment && Storage::exists($businessDevelopment->attachment)) {
                Storage::delete($businessDevelopment->attachment);
            }

            $inputs['attachment'] = $request->file('attachment')
                ->storeAs('tracker/business-development', time().'_'.random_int(1000, 9999).'_attachment.'.$request->file('attachment')->getClientOriginalExtension());
        }

        $record = $this->businessDevelopments->update($id, $inputs);

        if ($record) {
            return redirect()->route('business-development.index')->withSuccessMessage('Business Development updated successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Business Development could not be updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('manage-business-development');

        $record = $this->businessDevelopments->destroy($id);

        if ($record) {
            return response()->json([
                'type'      => 'success',
                'message'   => 'Business Development deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'type'      => 'error',
                'message'   => 'Business Development could not be deleted.'
            ], 422);
        }
    }
}
