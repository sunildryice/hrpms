<?php

namespace Modules\Tracker\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\Project\Models\Project;
use Modules\Tracker\Models\Enums\PostType;
use Modules\Tracker\Repositories\ResearchCommunicationRepository;
use Modules\Tracker\Requests\ResearchCommunication\StoreRequest;
use Modules\Tracker\Requests\ResearchCommunication\UpdateRequest;
use Yajra\DataTables\DataTables;

class ResearchCommunicationController extends Controller
{
    public function __construct(
        ResearchCommunicationRepository $researchCommunications,
    )
    {
        $this->researchCommunications = $researchCommunications;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $this->authorize('manage-research-communication');

        if ($request->ajax()) {
            $data = $this->researchCommunications->with(['project'])->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('type_of_publication', function ($row) {
                    return $row->getPublicationTypeLabel();
                })
                ->addColumn('project_title', function ($row) {
                    return $row->getProjectTitle();
                })
                ->addColumn('date_of_publication', function ($row) {
                    return $row->getDateOfPublication();
                })
                ->addColumn('date_posted', function ($row) {
                    return $row->getDatePosted();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('research-communication.show', $row->id) . '" rel="tooltip" title="View"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('manage-research-communication')) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('research-communication.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }

                    if ($authUser->can('manage-research-communication')) {
                        $btn .= '&emsp;<a href="javascript:;" class="btn btn-danger btn-sm delete-record" rel="tooltip" title="Delete" ';
                        $btn .= 'data-href="' . route('research-communication.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Tracker::ResearchCommunication.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage-research-communication');

        $postTypes = PostType::cases();
        $projects  = Project::whereNotNull('activated_at')->get();
        $employees = Employee::whereNotNull('activated_at')->orderBy('full_name')->get(['id', 'full_name']);

        return view('Tracker::ResearchCommunication.create', compact('postTypes', 'projects', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('manage-research-communication');

        $inputs = $request->validated();
        $inputs['created_by'] = auth()->user()->id;

        $record = $this->researchCommunications->create($inputs);

        if ($record) {
            return redirect()->route('research-communication.index')->withSuccessMessage('Research Uptake & Communication created successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Research Uptake & Communication could not be created.');
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
        $researchCommunication = $this->researchCommunications->find($id);
        return view('Tracker::ResearchCommunication.show', compact('researchCommunication'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('manage-research-communication');

        $researchCommunication = $this->researchCommunications->find($id);
        $postTypes = PostType::cases();
        $projects  = Project::whereNotNull('activated_at')->get();
        $employees = Employee::whereNotNull('activated_at')->orderBy('full_name')->get(['id', 'full_name']);

        return view('Tracker::ResearchCommunication.edit', compact('researchCommunication', 'postTypes', 'projects', 'employees'));
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
        $this->authorize('manage-research-communication');

        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->user()->id;

        $record = $this->researchCommunications->update($id, $inputs);

        if ($record) {
            return redirect()->route('research-communication.index')->withSuccessMessage('Research Uptake & Communication updated successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Research Uptake & Communication could not be updated.');
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
        $this->authorize('manage-research-communication');

        $record = $this->researchCommunications->destroy($id);

        if ($record) {
            return response()->json([
                'type'      => 'success',
                'message'   => 'Research Uptake & Communication deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'type'      => 'error',
                'message'   => 'Research Uptake & Communication could not be deleted.'
            ], 422);
        }
    }
}
