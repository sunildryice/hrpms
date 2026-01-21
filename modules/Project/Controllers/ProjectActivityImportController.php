<?php

namespace Modules\Project\Controllers;

use Illuminate\Http\Request;
use Modules\Project\Models\Project;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\Project\Imports\ActivityImport;
class ProjectActivityImportController extends Controller
{
    public function __construct(
        protected ProjectActivityRepository $projectActivity
    ) {
    }

    public function create(Request $request, Project $project)
    {
        return view('Project::ProjectActivity.import', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $fiscalYear = $project->fiscal_year;
        $userCode = auth()->user()->employee?->code ?? null;

        $file = $request->hasFile('attachment') ? $request->file('attachment') : null;
        try {
            Excel::import(new ActivityImport($fiscalYear, $userCode), $file, null, \Maatwebsite\Excel\Excel::XLSX);
            $response = ['message' => ' Activity imported successfully.'];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            logger()->error($th->getMessage());
            $response = ['message' => 'Failed to import activity. Please check the file and try again.'];
            return response()->json($response, 500);
        }
    }
}