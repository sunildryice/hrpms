<?php

namespace Modules\Master\Imports;

use DB;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\AccountCode;

class ActivityCodesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $authUser = auth()->user();

            if ($row['activity_code'] && $row['activity_name'] && $row['account_code'] && $row['account_name']) {
                $activityCode = ActivityCode::where('title', $row['activity_code'])->first();
                $accountCode = AccountCode::where('title', $row['account_code'])->first();

                if (!$activityCode) {
                    $activityCode = ActivityCode::create([
                        'title' => $row['activity_code'],
                        'description' => $row['activity_name'],
                        'activated_at' => date('Y-m-d H:i:s'),
                        'created_by' => $authUser->id,
                    ]);
                }
                if (!$accountCode) {
                    $accountCode = AccountCode::create([
                        'title' => $row['account_code'],
                        'description' => $row['account_name'],
                        'activated_at' => date('Y-m-d H:i:s'),
                        'created_by' => $authUser->id,
                    ]);
                }

                if ($activityCode && $accountCode) {
                    $activityCode->accountCodes()->sync([$accountCode->id]);
                }
            }

    }
}
