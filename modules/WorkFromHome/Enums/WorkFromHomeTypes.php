<?php

namespace Modules\WorkFromHome\Enums;


enum WorkFromHomeTypes: string
{
    case WORK_FROM_HOME = 'work_from_home';
    case FIELD_WORK = 'field_work';

    public function label(): string
    {
        return match ($this) {
            self::WORK_FROM_HOME => 'Work From Home',
            self::FIELD_WORK => 'Field Work',
        };
    }

    public static function options(): array
    {
        return [
            self::WORK_FROM_HOME->value => self::WORK_FROM_HOME->label(),
            self::FIELD_WORK->value => self::FIELD_WORK->label(),
        ];
    }
}
