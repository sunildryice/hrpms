<?php

namespace Modules\Project\Models\Enums;

enum ActivityStatus: string
{
    case NotStarted = 'not_started';
    case UnderProgess = 'under_progress';
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
