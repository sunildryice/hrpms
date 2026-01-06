<?php

namespace Modules\Project\Models\Enums;


enum ActivityLevel: string
{
    case Theme = 'theme';
    case Activity = 'activity';
    case SubActivity = 'sub_activity';
}
