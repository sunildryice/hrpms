<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Address;

class AddressRepository extends Repository
{
    public function __construct(Address $address)
    {
        $this->model = $address;
    }
}
