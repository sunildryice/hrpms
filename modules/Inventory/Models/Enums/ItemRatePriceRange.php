<?php

namespace Modules\Inventory\Models\Enums;

use Illuminate\Database\Eloquent\Builder;
use Modules\Inventory\Models\Asset;

enum ItemRatePriceRange: string
{
    case All = 'all';
    case Below5K = 'below_5k';
    case Above5K = 'above_5k';

    public function label(): string
    {
        return match ($this) {
            self::All => 'All',
            self::Below5K => 'Below 5,000',
            self::Above5K => 'Above 5,000',
        };
    }

    public function getPriceRange()
    {
        return match ($this) {
            self::Below5K => [0, 4999],
            self::Above5K => [5000, PHP_INT_MAX],
            default => null,
        };
    }

    public function apply(Builder $query): Builder
    {
        if (get_class($query->getModel()) != Asset::class || $this === self::All || $this->getPriceRange() == null) {
            return $query;
        }

        return $query->whereHas('inventoryItem', function ($q) {
            $q->whereBetween('unit_price', $this->getPriceRange());
        });

    }
}
