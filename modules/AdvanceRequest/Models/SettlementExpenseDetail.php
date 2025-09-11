<?php

namespace Modules\AdvanceRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;
use Modules\Master\Models\ExpenseType;
use Modules\Master\Models\ExpenseCategory;

class SettlementExpenseDetail extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'advance_settlement_expense_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'advance_settlement_id',
        'settlement_expense_id',
        'expense_date',
        'bill_number',
        'gross_amount',
        'tax_amount',
        'net_amount',
        'expense_category_id',
        'description',
        'expense_type_id',
        'attachment',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['expense_date'];

    /**
     * Get the expense category of the settlement expense detail.
     */
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id')->withDefault();
    }

    /**
     * Get the expense category of the settlement expense detail.
     */
    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id')->withDefault();
    }

    /**
     * Get the advance settlement of the settlement expense detail.
     */
    public function settlement()
    {
        return $this->belongsTo(Settlement::class, 'advance_settlement_id')->withDefault();
    }

    /**
     * Get the advance settlement  expense of the settlement expense detail.
     */
    public function settlementExpense()
    {
        return $this->belongsTo(SettlementExpense::class, 'settlement_expense_id')->withDefault();
    }

    public function getExpenseCategory()
    {
        return $this->expenseCategory->title;
    }

    public function getExpenseDate()
    {
        return $this->expense_date ? $this->expense_date->toFormattedDateString() : '';
    }

    public function getExpenseType()
    {
        return $this->expenseType->title;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
