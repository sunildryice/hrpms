<?php

namespace Modules\PerformanceReview\Models\Enums;

enum PerformanceOverallRating: int
{
    case NotAcceptable = 1;
    case NeedsImprovement = 2;
    case Successful = 3;
    case Exceptional = 4;

    public function label(): string
    {
        return match ($this) {
            self::NotAcceptable => '1 - Not Acceptable',
            self::NeedsImprovement => '2 - Needs Improvement',
            self::Successful => '3 - Successful Performance',
            self::Exceptional => '4 - Exceptional Performance',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::NotAcceptable => 'Not Acceptable',
            self::NeedsImprovement => 'Needs Improvement',
            self::Successful => 'Successful',
            self::Exceptional => 'Exceptional',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}
