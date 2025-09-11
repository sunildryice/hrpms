<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\PartnerOrganization;

class PartnerOrganizationRepository extends Repository
{
    public function __construct(PartnerOrganization $partner)
    {
        $this->model = $partner;
    }

    public function getActivePartnerOrgs()
    {
        return $this->model->select(['id', 'name'])
            ->where('is_active', 1)
            ->orderBy('name', 'asc')->get();
    }
}
