<?php

namespace Modules\Lta\Controllers\api;

use App\Http\Controllers\Controller;
use Modules\Lta\Repositories\LtaContractRepository;

class LtaController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param LtaContractRepository $contracts
     * @return void
     */
    public function __construct(
        LtaContractRepository $ltaContracts,
    ) {
        $this->ltaContracts = $ltaContracts;
    }

    public function fetch($supplierId)
    {
        $ltas = $this->ltaContracts
            ->select(['id', 'contract_number'])
            ->with(['ltaItems'])
            ->where('supplier_id', $supplierId)
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->get();
        return response()->json(['status' => 'ok', 'ltas' => $ltas], 200);
    }

}
