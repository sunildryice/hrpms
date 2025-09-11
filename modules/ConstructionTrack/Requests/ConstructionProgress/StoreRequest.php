<?php

namespace Modules\ConstructionTrack\Requests\ConstructionProgress;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Modules\ConstructionTrack\Repositories\ConstructionRepository;

class StoreRequest extends FormRequest
{
    private $constructions;
    public function __construct(
        ConstructionRepository $constructions
    )
    {
        $this->constructions = $constructions;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'report_date'=>'required',
            'progress_percentage'=>'required|numeric|min:0.01',
            'estimate'=>'nullable|string',
            'remarks'=>'required',
        ];
    }

    public function progressPercentageInvalid()
    {
        $validated = $this->validator->validated();
        $construction = $this->constructions->find($this->constructionId);
        if (($construction->constructionProgresses()->sum('progress_percentage')) + $validated['progress_percentage'] > 100) {
            return true;
        }
        return false;
    }

    public function validated($key = null, $default = null)
    {
        $validated = $this->validator->validated();

        // if ($this->progressPercentageInvalid()) {
        //     $response = response()->json([
        //         'errors' => [
        //             'progress_percentage' => [
        //                 'Total progress percentage cannot exceed 100%.'
        //             ]
        //         ]
        //     ], 422);
        //     throw new ValidationException($this->validator, $response);
        // }

        return data_get($validated, $key, $default);
    }

}
