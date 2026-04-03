<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Modules\Project\Repositories\ProjectActivityRepository;

class ClearProjectActivities extends Command
{
    protected $signature = 'dryice:clear:project:activities';

    protected $description = 'Clear project activities for a project that are duplicate.';

    public function __construct(
        protected ProjectActivityRepository $projectActivities,
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $projectActivities = $this->projectActivities->with(['children', 'timesheets'])
            ->where('project_id', 14)
            ->where('activity_level', '<>','theme')
            ->whereNull('parent_id')
            ->get();
        foreach($projectActivities as $activity){
            if($activity->timesheets->count()==0 && $activity->children->count()==0){
                $activity->delete();
                $this->info('Activity '. $activity->id .' deleted.');
            }
        }
    }
}
