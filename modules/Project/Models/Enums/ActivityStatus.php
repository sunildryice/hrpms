<?php

namespace Modules\Project\Models\Enums;

enum ActivityStatus: string
{
    case NotStarted = 'not_started';
    case UnderProgress = 'under_progress';
    case NoRequired = 'no_required';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            ActivityStatus::NotStarted => 'Not Started',
            ActivityStatus::UnderProgress => 'Under Progress',
            ActivityStatus::NoRequired => 'No Longer Required',
            ActivityStatus::Completed => 'Completed',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Completed => 'badge bg-success',
            self::UnderProgress => 'badge bg-warning',
            self::NotStarted => 'badge bg-orange text-white',
            self::NoRequired => 'badge bg-danger',
        };
    }
}
