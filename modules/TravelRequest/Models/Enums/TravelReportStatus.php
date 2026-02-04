<?php

namespace Modules\TravelRequest\Models\Enums;

enum TravelReportStatus: string
{
    case NotStarted = 'not_started';
    case UnderProgress = 'under_progress';
    case NoRequired = 'no_required';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            TravelReportStatus::NotStarted => 'Not Started',
            TravelReportStatus::UnderProgress => 'Under Progress',
            TravelReportStatus::NoRequired => 'No Required',
            TravelReportStatus::Completed => 'Completed',
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
