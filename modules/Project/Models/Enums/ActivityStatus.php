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
            ActivityStatus::NoRequired => 'No Required',
            ActivityStatus::Completed => 'Completed',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            ActivityStatus::NotStarted => 'badge bg-secondary',
            ActivityStatus::UnderProgress => 'badge bg-primary',
            ActivityStatus::NoRequired => 'badge bg-warning text-dark',
            ActivityStatus::Completed => 'badge bg-success',
        };
    }
}
