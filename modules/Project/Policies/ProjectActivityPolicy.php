<?php

namespace Modules\Project\Policies;

use Illuminate\Support\Facades\Gate;
use Modules\Privilege\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\ProjectActivity;

class ProjectActivityPolicy
{
    use HandlesAuthorization;

    public function update(User $user, ProjectActivity $projectActivity)
    {
        $teamLeadFocalPersonFlag = Gate::allows('manage-project-activity-on-certain-time', $projectActivity->project) &&
            ($projectActivity->status != ActivityStatus::NoRequired->value && $projectActivity->status != ActivityStatus::Completed->value &&
                Gate::allows('project-is-ongoing', $projectActivity->project));
        $projectAdminFlag = Gate::allows('manage-project-activity-project-admin', $projectActivity->project) &&
            Gate::allows('project-is-ongoing', $projectActivity->project);
        return $teamLeadFocalPersonFlag || $projectAdminFlag;
    }

    public function delete(User $user, ProjectActivity $projectActivity)
    {
        $flag = $this->update($user, $projectActivity);
        return $flag && $projectActivity->children->isEmpty() && $projectActivity->timesheets->count() == 0;
    }
}
