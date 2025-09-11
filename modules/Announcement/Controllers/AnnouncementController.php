<?php

namespace Modules\Announcement\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Announcement\Repositories\AnnouncementRepository;
use Modules\Announcement\Requests\StoreRequest;
use Modules\Announcement\Requests\UpdateRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Yajra\DataTables\DataTables;

class AnnouncementController extends Controller
{
    public function __construct(
        AnnouncementRepository  $announcements,
        FiscalYearRepository    $fiscalYears,
    )
    {
        $this->announcements    = $announcements;
        $this->fiscalYears      = $fiscalYears;
        $this->destinationPath  = 'announcement';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $this->authorize('manage-announcement');

        if ($request->ajax()) {
            $data = $this->announcements->orderBy('created_at', 'desc')->get();

            return DataTables::of($data) 
                ->addIndexColumn()
                ->addColumn('announcement_number', function($row) {
                    return $row->getAnnouncementNumber();
                })
                ->addColumn('title', function($row) {
                    return Str::limit($row->title, 30);
                })
                ->addColumn('published_date', function ($row) {
                    return $row->getPublishedDate();
                })
                ->addColumn('expiry_date', function ($row) {
                    return $row->getExpiryDate();
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatorName();
                })
                ->addColumn('attachment', function ($row) {
                    $attachment = '';
                    if (isset($row->attachment)) {
                        $attachment  = '<a class="btn btn-sm btn-outline-primary" href="';
                        $attachment .= asset('storage/'.$row->attachment).'" rel="tooltip" title="View Attachment" target="_blank"><i class="bi bi-file-earmark-text"></i></a>';
                    }
                    return $attachment;
                })
                ->addColumn('action', function ($row) use($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('announcement.show', $row->id).'" rel="tooltip" title="View Announcement"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('manage-announcement')) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('announcement.edit', $row->id).'" rel="tooltip" title="Edit Announcement"><i class="bi-pencil-square"></i></a>';
                    }

                    if ($authUser->can('manage-announcement')) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" rel="tooltip" title="Delete Performance Review" ';
                        $btn .= 'data-href="' . route('announcement.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'attachment'])
                ->make(true);
        }

        return view('Announcement::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage-announcement');

        return view('Announcement::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('manage-announcement');

        $inputs = $request->validated();

        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                                ->storeAs($this->destinationPath, time().'_'.random_int(1000, 9999).'_announcement_attachment.'.$request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }

        $inputs['fiscal_year_id']       = $this->fiscalYears->getCurrentFiscalYearId();
        $inputs['prefix']               = 'ANC';
        $inputs['announcement_number']  = $this->announcements->getAnnouncementNumber();
        $inputs['created_by']           = auth()->user()->id;
        
        $announcement = $this->announcements->create($inputs);

        if ($announcement) {
            return redirect()->route('announcement.index')->withSuccessMessage('Announcement created successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Announcement could not be created.');
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
        $announcement = $this->announcements->find($id);
        return view('Announcement::show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('manage-announcement');

        $announcement = $this->announcements->find($id);
        return view('Announcement::edit', compact('announcement'));
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
        $this->authorize('manage-announcement');
        
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                                ->storeAs($this->destinationPath, time().'_'.random_int(1000, 9999).'_announcement_attachment.'.$request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $announcement = $this->announcements->update($id, $inputs);

        if ($announcement) {
            return redirect()->route('announcement.index')->withSuccessMessage('Announcement updated successfully.');
        } else {
            return redirect()->back()->withInput()->withWarningMessage('Announcement could not be updated.');
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
        $this->authorize('manage-announcement');
        
        $announcement = $this->announcements->destroy($id);

        if ($announcement) {
            return response()->json([
                'type'      => 'success',
                'message'   => 'Announcement deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'type'      => 'error',
                'message'   => 'Announcement could not be deleted.'
            ], 422);
        }
    }
}
