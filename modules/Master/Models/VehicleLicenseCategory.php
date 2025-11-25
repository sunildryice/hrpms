<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;

class VehicleLicenseCategory extends Model
{
    use HasFactory, ModelEventLogger;
    protected $table = 'lkup_vehicle_license_categories';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}