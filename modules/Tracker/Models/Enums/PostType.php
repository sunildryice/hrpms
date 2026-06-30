<?php

namespace Modules\Tracker\Models\Enums;

enum PostType: string
{
    case Blog = 'Blog';
    case Graphic = 'Graphic';
    case Insight = 'Insight';
    case JournalArticle = 'Journal Article';
    case Newsletter = 'Newsletter';
    case Photos = 'Photos';
    case Podcast = 'Podcast';
    case VacancyAnnouncement = 'Vacancy Announcement';
    case Video = 'Video';

    public function label(): string
    {
        return $this->value;
    }
}
