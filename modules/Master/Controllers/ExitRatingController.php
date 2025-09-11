<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ExitRatingRepository;
use Modules\Master\Requests\ExitRating\StoreRequest;
use Modules\Master\Requests\ExitRating\UpdateRequest;

use DataTables;

class ExitRatingController extends Controller
{
    /**
     * The exit rating repository instance.
     *
     * @var ExitRatingRepository
     */
    protected $exitRatings;

    /**
     * Create a new controller instance.
     *
     * @param ExitRatingRepository $exitRatings
     * @return void
     */
    public function __construct(
        ExitRatingRepository $exitRatings
    )
    {
        $this->exitRatings = $exitRatings;
    }

    /**
     * Display a listing of the exit rating.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->exitRatings->select([
                'id', 'title', 'created_by', 'updated_at'
            ]);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-ratings-modal-form" href="';
                    $btn .= route('master.exit.ratings.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.exit.ratings.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::ExitRating.index');
    }

    /**
     * Show the form for creating a new exit rating.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Master::ExitRating.create');
    }

    /**
     * Store a newly created exit rating in storage.
     *
     * @param \Modules\Master\Requests\ExitRating\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $exitRating = $this->exitRatings->create($inputs);
        if ($exitRating) {
            return response()->json(['status' => 'ok',
                'exitRating' => $exitRating,
                'message' => 'Exit rating is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit rating can not be added.'], 422);
    }

    /**
     * Display the specified exit rating.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $exitRating = $this->exitRatings->find($id);
        return response()->json(['status' => 'ok', 'exitRating' => $exitRating], 200);
    }

    /**
     * Show the form for editing the specified exit rating.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $exitRating = $this->exitRatings->find($id);
        return view('Master::ExitRating.edit')
            ->withExitRating($exitRating);
    }

    /**
     * Update the specified exit rating in storage.
     *
     * @param \Modules\Master\Requests\ExitRating\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $exitRating = $this->exitRatings->update($id, $inputs);
        if ($exitRating) {
            return response()->json(['status' => 'ok',
                'exitRating' => $exitRating,
                'message' => 'Exit rating is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Exit rating can not be updated.'], 422);
    }

    /**
     * Remove the specified exit rating from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->exitRatings->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Exit rating is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Exit rating can not deleted.',
        ], 422);
    }
}
