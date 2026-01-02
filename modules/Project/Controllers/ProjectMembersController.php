<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Project\Requests\ProjectMembers\UpdateRequest;

class ProjectMembersController extends Controller
{
    public function __construct(
        protected ProjectRepository $projectRepository,
    ) {}

    public function update($id, UpdateRequest $request)
    {
        $inputs = $request->validated();

        $project = $this->projectRepository->find($id);

        $this->projectRepository->update($id, $inputs);
        $project->members()->sync($inputs['members']);

        return redirect()
            ->route('project.edit', $id)
            ->withSuccessMessage('Project members updated successfully.');
    }
}
