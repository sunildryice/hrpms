<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\FiscalYear;

class FiscalYearRepository extends Repository
{
    public function __construct(FiscalYear $fiscalYear)
    {
        $this->model = $fiscalYear;
    }

    public function getFiscalYears()
    {
        return $this->model->get();
    }

    public function getCurrentFiscalYear()
    {
        return $this->model->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
    }

    public function getFiscalYearOfDate($date)
    {
        return $this->model->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();
    }

    public function getCurrentFiscalYearId()
    {
        return $this->getCurrentFiscalYear()->id;
    }

    public function getCurrentFiscalYearTitle()
    {
        return $this->getCurrentFiscalYear()->title;
    }

    public function getFiscalYearById($id)
    {
        return $this->find($id)->title;
    }

    public function getFiscalYearIdByTitle($title)
    {
        return $this->model->where('title', $title)->first()->id;
    }

    public function getPreviousFiscalYear()
    {
        $previousFY = $this->getCurrentFiscalYearTitle() - 1;
        return $this->model->where('title', $previousFY)->first();
    }

    public function getMinStartDate()
    {
        return $this->model->min('start_date');
    }

    public function getMaxEndDate()
    {
        return $this->model->max('end_date');
    }
}
