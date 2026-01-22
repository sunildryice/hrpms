<?php

namespace Modules\Project\Requests\ActivityUpdatePeriod;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Project\Models\ActivityUpdatePeriod;
use Carbon\Carbon;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $data['is_active'] = !empty($data['is_active']);
        return $data;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('start_date');
            $end = $this->input('end_date');
            if (!$start || !$end) {
                return;
            }

            $overlaps = ActivityUpdatePeriod::query()
                ->whereDate('start_date', '<=', $end)
                ->whereDate('end_date', '>=', $start)
                ->get(['start_date', 'end_date']);

            if ($overlaps->isNotEmpty()) {
                $ranges = $overlaps->map(function ($row) {
                    $s = Carbon::parse($row->start_date)->format('M j, Y');
                    $e = Carbon::parse($row->end_date)->format('M j, Y');
                    return "$s - $e";
                })->implode(', ');
                $message = 'The date range overlaps existing update period(s): ' . $ranges . '.';
                $validator->errors()->add('start_date', $message);
            }
        });
    }
}
