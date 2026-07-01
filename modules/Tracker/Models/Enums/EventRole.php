<?php

namespace Modules\Tracker\Models\Enums;

enum EventRole: string
{
    case Participant = 'Participant';
    case Speaker = 'Speaker';
    case Panelist = 'Panelist';
    case Facilitator = 'Facilitator';
    case Trainer = 'Trainer';
    case Trainee = 'Trainee';
    case ResourcePerson = 'Resource Person';
    case Other = 'Other';

    public function label(): string
    {
        return $this->value;
    }
}
