<?php

namespace Modules\Project\Models\Enums;

enum ActivityStatus: string
{
    case NotStarted = 'not_started';
    case UnderProgess = 'under_progress';
    case NoRequired = 'no_required';
    case Completed = 'completed';
}
