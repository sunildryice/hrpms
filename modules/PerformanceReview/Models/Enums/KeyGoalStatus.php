<?php

namespace Modules\PerformanceReview\Models\Enums;

enum KeyGoalStatus: string
{
    case PartiallyCompleted = 'partially_completed';
    case FullyCompleted = 'fully_completed';

    public function label(): string
    {
        return match ($this) {
            KeyGoalStatus::PartiallyCompleted => 'Partially Completed',
            KeyGoalStatus::FullyCompleted => 'Fully Completed',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::FullyCompleted => 'badge bg-success',
            self::PartiallyCompleted => 'badge bg-warning',
        };
    }
}
