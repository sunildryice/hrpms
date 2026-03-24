<?php

namespace Modules\PerformanceReview\Models\Enums;

enum KeyGoalStatus: string
{
    case NotCompleted = 'not_completed';
    case PartiallyCompleted = 'partially_completed';
    case FullyCompleted = 'fully_completed';

    public function label(): string
    {
        return match ($this) {
            KeyGoalStatus::NotCompleted => 'Not Completed',
            KeyGoalStatus::PartiallyCompleted => 'Partially Completed',
            KeyGoalStatus::FullyCompleted => 'Fully Completed',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::NotCompleted => 'badge bg-secondary text-white',
            self::PartiallyCompleted => 'badge bg-warning',
            self::FullyCompleted => 'badge bg-success',
        };
    }
}
