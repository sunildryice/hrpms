<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Privilege\Models\User;
use Modules\Project\Models\ActivityStage;
use Modules\Project\Models\Enums\ActivityLevel;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectActivity;

class ProjectGanttChartSeeder extends Seeder
{
    /**
     * Seed realistic 2026 projects with stages, activities
     * and nested child sub-activities for testing the Gantt chart.
     * Each run creates a new project with a unique short name.
     *
     * @return void
     */
    public function run()
    {
        // Basic guards so the seeder doesn't fail on empty prerequisite data
        $users = User::orderBy('id')->take(5)->get();
        $stages = ActivityStage::orderBy('id')->take(5)->get();

        if ($users->isEmpty() || $stages->isEmpty()) {
            return;
        }

        // Use Faker to generate project title and short name
        $faker = Faker::create();

        // Generate a reasonably short code like "NRD-2026-AB12"
        $shortName = null;
        do {
            $candidate = strtoupper('NRD-2026-' . $faker->bothify('??##'));
        } while (Project::where('short_name', $candidate)->exists());

        $shortName = $candidate;

        $teamLead = $users->first();
        $focalPerson = $users->skip(1)->first() ?: $teamLead;

        $domainWord = $faker->catchPhrase();
        $title = 'National ' . $domainWord . ' Research & Development';

        $project = Project::create([
            'title'            => $title,
            'short_name'       => $shortName,
            'description'      => 'Flagship national research and development programme running across 2026 with multiple thematic workstreams and field activities.',
            'start_date'       => Carbon::create(2026, 1, 1)->toDateString(),
            'completion_date'  => Carbon::create(2026, 12, 30)->toDateString(),
            'team_lead_id'     => $teamLead->id,
            'focal_person_id'  => $focalPerson->id,
        ]);

        // Attach core project members
        $memberIds = $users->pluck('id')->all();

        foreach ($memberIds as $userId) {
            DB::table('project_members')->insertOrIgnore([
                'project_id' => $project->id,
                'user_id'    => $userId,
            ]);
        }

        // Attach activity stages to the project
        foreach ($stages as $stage) {
            DB::table('project_activity_stages')->insertOrIgnore([
                'project_id'       => $project->id,
                'activity_stage_id' => $stage->id,
            ]);
        }

        // Define phase ranges across the project year
        $phaseRanges = [
            ['start' => '2026-01-01', 'end' => '2026-03-31'],
            ['start' => '2026-04-01', 'end' => '2026-06-30'],
            ['start' => '2026-07-01', 'end' => '2026-09-30'],
            ['start' => '2026-10-01', 'end' => '2026-11-15'],
            ['start' => '2026-11-16', 'end' => '2026-12-30'],
        ];

        // For each stage, create a theme (parent), activities (children)
        // and sub-activities (grandchildren) to mimic nested structures
        foreach ($stages as $index => $stage) {
            $rangeIndex = $index < count($phaseRanges) ? $index : count($phaseRanges) - 1;
            $range = $phaseRanges[$rangeIndex];

            $stageStart = Carbon::parse($range['start']);
            $stageEnd = Carbon::parse($range['end']);

            $themeNumber = $index + 1;
            $themeTitle = 'Theme ' . $themeNumber . ': ' . $stage->title;

            // Top-level theme for the stage
            $theme = ProjectActivity::create([
                'project_id'        => $project->id,
                'activity_stage_id' => $stage->id,
                'activity_level'    => ActivityLevel::Theme->value,
                'parent_id'         => null,
                'title'             => $themeTitle,
                'deliverables'      => 'High-level planning, coordination and oversight activities for ' . strtolower($stage->title) . '.',
                'budget_description' => 'Core programme resources allocated for coordination, supervision and reporting.',
                'status'            => ActivityStatus::UnderProgress->value,
                'start_date'        => $stageStart->toDateString(),
                'completion_date'   => $stageEnd->toDateString(),
                'created_by'        => $teamLead->id,
                'updated_by'        => $teamLead->id,
            ]);

            // Divide the stage period into two activity segments
            $totalDays = max($stageStart->diffInDays($stageEnd), 1);
            $activitySegmentLength = (int) floor($totalDays / 2) ?: 1;

            for ($a = 0; $a < 2; $a++) {
                $activityStart = (clone $stageStart)->addDays($activitySegmentLength * $a);
                $activityEnd = $a === 1
                    ? (clone $stageEnd)
                    : (clone $activityStart)->addDays($activitySegmentLength - 1);

                if ($activityEnd->gt($stageEnd)) {
                    $activityEnd = (clone $stageEnd);
                }

                $activityTitle = match ($a) {
                    0 => 'Operational research for ' . strtolower($stage->title),
                    1 => 'Pilot implementation for ' . strtolower($stage->title),
                    default => 'Activity ' . ($a + 1) . ' for ' . strtolower($stage->title),
                };

                // Mid-level activity under the theme
                $activity = ProjectActivity::create([
                    'project_id'        => $project->id,
                    'activity_stage_id' => $stage->id,
                    'activity_level'    => ActivityLevel::Activity->value,
                    'parent_id'         => $theme->id,
                    'title'             => $activityTitle,
                    'deliverables'      => 'Stage-specific implementation and coordination outputs.',
                    'budget_description' => 'Resources for teams, logistics and supervision.',
                    'status'            => ActivityStatus::UnderProgress->value,
                    'start_date'        => $activityStart->toDateString(),
                    'completion_date'   => $activityEnd->toDateString(),
                    'created_by'        => $focalPerson->id,
                    'updated_by'        => $focalPerson->id,
                ]);

                // Within each activity, create two detailed sub-activities
                $activityDays = max($activityStart->diffInDays($activityEnd), 1);
                $subSegmentLength = (int) floor($activityDays / 2) ?: 1;

                for ($i = 0; $i < 2; $i++) {
                    $subStart = (clone $activityStart)->addDays($subSegmentLength * $i);
                    $subEnd = $i === 1
                        ? (clone $activityEnd)
                        : (clone $subStart)->addDays($subSegmentLength - 1);

                    if ($subEnd->gt($activityEnd)) {
                        $subEnd = (clone $activityEnd);
                    }

                    $subTitle = match (true) {
                        $a === 0 && $i === 0 => 'Protocol design & tools development for ' . strtolower($stage->title),
                        $a === 0 && $i === 1 => 'Ethics approval & stakeholder consultation for ' . strtolower($stage->title),
                        $a === 1 && $i === 0 => 'Field data collection & supervision for ' . strtolower($stage->title),
                        $a === 1 && $i === 1 => 'Data analysis, synthesis & dissemination for ' . strtolower($stage->title),
                        default               => 'Sub-activity ' . ($i + 1) . ' under ' . strtolower($stage->title),
                    };

                    $subActivity = ProjectActivity::create([
                        'project_id'        => $project->id,
                        'activity_stage_id' => $stage->id,
                        'activity_level'    => ActivityLevel::SubActivity->value,
                        'parent_id'         => $activity->id,
                        'title'             => $subTitle,
                        'deliverables'      => 'Detailed deliverables including reports, field outputs and knowledge products.',
                        'budget_description' => 'Operational costs for field teams, analysis and reporting.',
                        'status'            => ActivityStatus::UnderProgress->value,
                        'start_date'        => $subStart->toDateString(),
                        'completion_date'   => $subEnd->toDateString(),
                        'created_by'        => $focalPerson->id,
                        'updated_by'        => $focalPerson->id,
                    ]);

                    // Assign 2-3 members to each sub-activity for Gantt popups
                    $assignedMembers = $users->random(min(3, $users->count()))->pluck('id')->all();

                    foreach ($assignedMembers as $userId) {
                        DB::table('project_activity_members')->insertOrIgnore([
                            'activity_id' => $subActivity->id,
                            'user_id'     => $userId,
                        ]);
                    }
                }
            }
        }
    }
}
