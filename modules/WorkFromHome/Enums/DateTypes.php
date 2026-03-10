<?php

namespace Modules\WorkFromHome\Enums;


class DateTypes
{
    const FULL_DAY = 'Full Day';
    const FIRST_HALF = 'First Half';
    const SECOND_HALF = 'Second Half';

    public static function options(): array
    {
        return [
            self::FULL_DAY => 'Full Day',
            self::FIRST_HALF => 'First Half',
            self::SECOND_HALF => 'Second Half',
        ];
    }
}