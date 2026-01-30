<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Imports\ActivityImport;

class ProjectActivityImportController extends Controller
{
    public function __construct(
        protected ProjectActivityRepository $projectActivity
    ) {}

    public function create(Request $request, Project $project)
    {
        return view('Project::ProjectActivity.import', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'attachment' => 'required|max:10240|mimes:xlsx'
        ], [
            'attachment.required' => 'Please choose the file.',
            'attachment.max' => 'File size cannot exceed :max KB',
            'attachment.mimes' => 'Please upload excel file!'
        ]);

        $file = $request->hasFile('attachment') ? $request->file('attachment') : null;
        try {

            DB::beginTransaction();

            Excel::import(new ActivityImport($project), $file, null, \Maatwebsite\Excel\Excel::XLSX);
            $response = ['message' => ' Activity imported successfully.'];

            DB::commit();

            return response()->json($response, 200);
        } catch (\Throwable $th) {

            DB::rollBack();
            dd($th);
            $response = ['message' => 'Failed to import activity. Please check the file and try again.'];
            return response()->json($response, 500);
        }
    }
}
