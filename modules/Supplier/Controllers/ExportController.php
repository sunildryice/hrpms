<?php

namespace Modules\Supplier\Controllers;

use App\Http\Controllers\Controller;

use Modules\Supplier\Exports\SupplierExport;

class ExportController extends Controller
{
    public function export()
    {
        return new SupplierExport;
    }
}