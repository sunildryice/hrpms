<?php

namespace Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Requests\Asset\Recover\StoreRequest;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Master\Repositories\OfficeRepository;

class AssetRecoverController extends Controller
{
    private $assets;
    /**
     * Create a new controller instance.
     *
     * @param AssetRepository $assets
     */
    public function __construct(
        AssetRepository $assets,
        protected OfficeRepository $offices
    )
    {
        $this->assets = $assets;
    }

    public function create($id)
    {
        $authUser = auth()->user();
        $this->authorize('manage-asset-logistic');
        $asset = $this->assets->find($id);
        $offices = $this->offices->getActiveOffices();

        return view('Inventory::Asset.Recover.create', compact('asset', 'offices'));
    }

    public function store(StoreRequest $request, $id)
    {
        $this->authorize('manage-asset-logistic');
        $asset = $this->assets->find($id);
        $inputs = $request->validated();

        $asset = $this->assets->recover($id, $inputs);
        if($asset){
            return response()->json([
                'type' => 'Success',
                'message' => 'Asset Reclaimed',
            ], 200);
        }
        return response()->json([
            'type' => 'Error',
            'message' => 'Asset couldnt be relaimed',
        ], 422);


    }

}
