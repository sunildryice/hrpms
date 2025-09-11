<?php
namespace Modules\Supplier\Repositories;

use App\Repositories\Repository;
use Modules\Supplier\Models\Supplier;

class SupplierRepository extends Repository
{
    public function __construct(Supplier $supplier)
    {
        $this->model = $supplier;
    }

    public function getActiveSuppliers()
    {
        return $this->model->select(['id', 'supplier_name', 'vat_pan_number'])
            ->whereNotNull('activated_at')
            ->orderBy('supplier_name')
            ->get();
    }
}
