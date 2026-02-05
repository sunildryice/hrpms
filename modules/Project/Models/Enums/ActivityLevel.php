<?php

namespace Modules\Project\Models\Enums;


enum ActivityLevel: string
{
    case Theme = 'theme';
    case Activity = 'activity';
    case SubActivity = 'sub_activity';

    public static function toArray(): array
    {
        return [
            self::Theme->value => 'Theme',
            self::Activity->value => 'Activity',
            self::SubActivity->value => 'Sub Activity',
        ];
    }
}
