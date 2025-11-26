<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\SocialAccount;

class SocialMediaAccountRepository extends Repository
{

    public function __construct(SocialAccount $socialAccount)
    {
        $this->model = $socialAccount;
    }
}
