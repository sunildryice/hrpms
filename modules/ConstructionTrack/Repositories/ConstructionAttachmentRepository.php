<?php

namespace Modules\ConstructionTrack\Repositories;
use App\Repositories\Repository;
use Modules\ConstructionTrack\Models\ConstructionAttachment;

class ConstructionAttachmentRepository extends Repository
{
    public function __construct(
        ConstructionAttachment $constructionAttachment
    )
    {
        $this->model = $constructionAttachment;
    }

    
}