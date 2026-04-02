<?php

namespace Modules\Project\Models\Enums;

enum WorkPlanStatus: string
{

    case Completed = 'completed';
    case PartiallyCompleted = 'partially_completed';
    case NotStarted = 'not_started';
    case NoRequired = 'no_required';

    public function label(): string
    {
        return match ($this) {
            WorkPlanStatus::NotStarted => 'Not Started',
            WorkPlanStatus::PartiallyCompleted => 'Ongoing',
            WorkPlanStatus::Completed => 'Completed',
            WorkPlanStatus::NoRequired => 'Not Required',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Completed => 'badge bg-success',
            self::PartiallyCompleted => 'badge bg-warning',
            self::NotStarted => 'badge bg-secondary',
            self::NoRequired => 'badge bg-info',
        };
    }
}
