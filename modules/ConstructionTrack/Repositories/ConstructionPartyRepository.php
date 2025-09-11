<?php

namespace Modules\ConstructionTrack\Repositories;

use App\Repositories\Repository;
use Modules\ConstructionTrack\Models\ConstructionParty;

use DB;
use Modules\ConstructionTrack\Models\Construction;

class ConstructionPartyRepository extends Repository
{
    public function __construct(
        ConstructionParty $constructionParty,
        protected Construction      $construction
    ){
        $this->model        = $constructionParty;
    }

    public function updateContributionPercentage($constructionId)
    {
        $construction = $this->construction->find($constructionId);
        $construction_parties = $this->model->where('construction_id', $constructionId);
        $totalContributionAmount = $construction_parties->sum('contribution_amount');
        $constructionParties = $construction_parties->get();
        foreach ($constructionParties as $constructionParty) {
            $constructionParty->fill([
                'contribution_percentage' => $this->calculatePercentage($totalContributionAmount, $constructionParty->contribution_amount)
            ])->save();
            if ($constructionParty->deletable == 0) {
                $construction->fill([
                    'ohw_contribution'              => $constructionParty->contribution_amount,
                    'total_contribution_amount'     => $construction->getTotalContributionAmount(),
                    'total_contribution_percentage' => $constructionParty->contribution_percentage
                ])->save();
            }
        }
    }

    public function calculatePercentage($totalContributionAmount, $contributionAmount)
    {
        $percentage = ( $contributionAmount / $totalContributionAmount ) * 100;
        // $percentageString = substr(strval($percentage), 0, 5);
        $percentageString = round(strval($percentage), 2);

        return $percentageString;
    }


}
