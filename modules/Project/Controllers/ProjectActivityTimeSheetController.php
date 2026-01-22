<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Repositories\ProjectActivityRepository;
class ProjectActivityTimeSheetController extends Controller
{
    public function __construct(
        protected ProjectActivityRepository $projectActivity
    )
    {
        $this->projectActivity = $projectActivity;
    }
    public function create(ProjectActivity $projectActivity)
    {
        return view('Project::ProjectActivityTimeSheet.create', compact('projectActivity'));
    }
}