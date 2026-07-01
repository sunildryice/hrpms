<?php

namespace Modules\Tracker\Models\Enums;

enum Ethnicity: string
{
    case BrahminChhetri = 'Brahmin/Chhetri';
    case Janajati = 'Janajati';
    case Madheshi = 'Madheshi';
    case Dalits = 'Dalits';
    case Muslims = 'Muslims';
    case Others = 'Others';

    public function label(): string
    {
        return $this->value;
    }
}
