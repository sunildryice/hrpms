<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Currency;

class CurrencyRepository extends Repository
{
    public function __construct(
        Currency $currency
    )
    {
        $this->model = $currency;
    }

    public function getCurrencies()
    {
        return $this->model->whereNotNull('activated_at')->get();
    }
}