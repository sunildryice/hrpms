<?php

namespace Modules\Payroll\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\PaymentMaster;
use Modules\Payroll\Models\PayrollBatch;
use Modules\Payroll\Models\PayrollFiscalYear;
use Modules\Payroll\Models\PayrollSheet;
use Modules\Payroll\Models\TaxDiscount;
use Modules\Payroll\Models\TaxRate;

class PayrollSheetRepository extends Repository
{
    /**
     * @param Employee $employees
     * @param PayrollSheet $payrollSheets
     * @param PayrollBatch $payrollBatches
     * @param PaymentMaster $paymentMasters
     * @param TaxDiscount $taxDiscounts
     * @param TaxRate $taxRates
     */
    public function __construct(
        Employee      $employees,
        PayrollSheet  $payrollSheets,
        PayrollBatch  $payrollBatches,
        PaymentMaster $paymentMasters,
        TaxDiscount $taxDiscounts,
        TaxRate       $taxRates
    )
    {
        $this->employees = $employees;
        $this->payrollBatches = $payrollBatches;
        $this->model = $payrollSheets;
        $this->paymentMasters = $paymentMasters;
        $this->taxDiscounts = $taxDiscounts;
        $this->taxRates = $taxRates;
    }

    protected function calculateAnnualBenefitAmount($payrollSheet, $pastPayrollSheets, $paymentMaster)
    {
        $employee = $payrollSheet->employee;
        $pastMonthBenefitAmount = array_sum(array_column($pastPayrollSheets->past_payroll_sheets, 'gross_amount'));
        $pastMonthCount = $pastPayrollSheets->past_month_count;
        $futureMonthCount = 12 - $pastMonthCount - 1;

        $benefitAmount = $payrollSheet->details->filter(function ($detail) {
            return $detail->paymentItem->type == 'B' && $detail->paymentItem->frequency == 12;
        })->sum('amount');

        $oneTimeBenefitAmount = $paymentMaster->paymentDetails->filter(function ($detail) {
            return $detail->paymentItem->type == 'B' && $detail->paymentItem->frequency == 1;
        })->sum('amount');

        $currentMonthBenefitAmount = $benefitAmount;
        $futureMonthBenefitAmount = $benefitAmount*$futureMonthCount;
        $annualBenefitAmount = $pastMonthBenefitAmount + $currentMonthBenefitAmount + $futureMonthBenefitAmount + $oneTimeBenefitAmount;

        return $annualBenefitAmount;
    }

    protected function calculateAnnualDeductionAmount($payrollSheet, $pastPayrollSheets)
    {
        $employee = $payrollSheet->employee;
        $pastMonthDeductionAmount = array_sum(array_column($pastPayrollSheets->past_payroll_sheets, 'total_deduction_amount'));
        $pastMonthCount = $pastPayrollSheets->past_month_count;
        $futureMonthCount = 12 - $pastMonthCount - 1;
        $deductionAmount = $payrollSheet->details->filter(function ($detail) {
            return $detail->paymentItem->type == 'D';
        })->sum('amount');

        $currentMonthDeductionAmount = $deductionAmount;
        $futureMonthDeductionAmount = $deductionAmount*$futureMonthCount;

        $annualDeductionAmount = $pastMonthDeductionAmount + $currentMonthDeductionAmount + $futureMonthDeductionAmount;
        $annualNonTaxableDeductionAmount = $annualDeductionAmount <= 500000 ? $annualDeductionAmount : 500000;
        $remoteDeductionAmount = $this->calculateAnnualRemoteDeductionAmount($payrollSheet);
        $insuranceDeductionAmount = $this->calculateAnnualInsuranceDeductionAmount($payrollSheet);
        $disabledDeductionAmount = $this->calculateAnnualDisabledDeductionAmount($payrollSheet);
        $annualNonTaxableDeductionAmount = $annualNonTaxableDeductionAmount+$remoteDeductionAmount+$insuranceDeductionAmount+$disabledDeductionAmount;

        return json_encode([
            'annualDeductionAmount'=>$annualDeductionAmount,
            'annualNonTaxableDeductionAmount'=>$annualNonTaxableDeductionAmount,
            'annualTaxableDeductionAmount'=>$annualDeductionAmount-$annualNonTaxableDeductionAmount,
        ]);
    }

    protected function calculateAnnualDisabledDeductionAmount($payrollSheet)
    {
        $amount = 0;
        $employee = $payrollSheet->employee;
        if($employee->finance->disabled){
            $married = ucwords($employee->maritalStatus->title) == 'Married' ? 1 : 0;
            $taxRate = $this->taxRates->select(['*'])
                ->where('payroll_fiscal_year_id', $payrollSheet->payrollBatch->payroll_fiscal_year_id)
                ->where('married', $married)
                ->orderBy('tax_rate', 'asc')
                ->first();
            $amount = $taxRate->annual_income_to*50/100;
        }
        return $amount;
    }

    protected function calculateAnnualInsuranceDeductionAmount($payrollSheet)
    {
        $employee = $payrollSheet->employee;
        $payrollFiscalYearId = $payrollSheet->payrollBatch->payroll_fiscal_year_id;
        $insurance = $employee->insurances->filter(function ($insurance) use ($payrollFiscalYearId){
            return $insurance->payroll_fiscal_year_id == $payrollFiscalYearId;
        })->first();
        $amount = 0;
        if($insurance){
            $amount = ($insurance->amount <= 40000) ? $insurance->amount : 40000;
        }
        return $amount;
    }

    protected function calculateAnnualRemoteDeductionAmount($payrollSheet)
    {
        $amount = 0;
        $employee = $payrollSheet->employee;
        if($employee->finance->created_at){
            $category = 'remote-'.strtolower($employee->finance->remote_category);
            $taxDiscount = $this->taxDiscounts->select(['*'])->where('slug', $category)->first();
            $amount = $taxDiscount ?->discount_amount_to;
        }
        return $amount;
    }

    protected function calculateMonthlyBenefitAmount($payrollSheet, $paymentMaster)
    {
        $paymentDetails = $paymentMaster->paymentDetails;

//        $paymentMaster = $this->paymentMasters->with(['paymentDetails'])
//            ->where('start_date', '<=', $checkDate)
//            ->where('end_date', '>=', $checkDate)
//            ->where('employee_id', $employee->id)
//            ->first();
        return $paymentMaster->paymentDetails->filter(function ($master) {
            return $master->paymentItem->type == 'B' && $master->paymentItem->frequency == 12;
        })->sum('amount');
    }

    protected function calculateMonthlyDeductionAmount($payrollSheet, $paymentMaster)
    {
        $employee = $payrollSheet->employee;
        $benefitAmount = $paymentMaster->paymentDetails->filter(function ($master) {
            return $master->paymentItem->type == 'D';
        })->sum('amount');
        return $benefitAmount;
    }

    protected function calculateSingleFemaleTaxDiscount($taxAmount)
    {
        return $taxAmount*0.1;
    }

    protected function calculateTaxAmounts($taxableAmount, $payrollSheet, $pastPayrollSheets)
    {
        $employee = $payrollSheet->employee;
        $totalSstAmount = $totalTaxAmount = $previousSlabAmount = 0;
        $married = ucwords($employee->maritalStatus->title) == 'Married' ? 1 : 0;
        $taxRates = $this->taxRates->select(['*'])
            ->where('payroll_fiscal_year_id', $payrollSheet->payrollBatch->payroll_fiscal_year_id)
            ->where('married', $married)
            ->orderBy('tax_rate', 'asc')
            ->get();
        $annualReceivableAmount = $taxableAmount;

        foreach ($taxRates as $taxRate) {
            $slabAmount = $taxRate->annual_income_to - $previousSlabAmount;
            if($annualReceivableAmount > 0){
                $slabAmount = ($slabAmount >= $annualReceivableAmount) ? $annualReceivableAmount : $slabAmount;
                $slabTaxAmount = $slabAmount * $taxRate->tax_rate /100;
                if ($taxRate->tax_rate > 1) {
                    $totalTaxAmount += $slabTaxAmount;
                } else {
                    $totalSstAmount += $slabTaxAmount;
                }
            }
            $annualReceivableAmount = $annualReceivableAmount - $slabAmount;
            $previousSlabAmount = $taxRate->annual_income_to;
        }
        $totalTaxDiscountAmount = 0;
        $totalTaxLiability = $totalTaxAmount;
        if($married == 0 && ucwords($employee->employeeGender->title) == 'Female'){
            $totalTaxDiscountAmount = $this->calculateSingleFemaleTaxDiscount($totalTaxAmount);
            $totalTaxAmount = $totalTaxAmount - $totalTaxDiscountAmount;
        }

        $pastTaxLiabilityAmount = array_sum(array_column($pastPayrollSheets->past_payroll_sheets, 'tax_liability'));
        $pastTaxDiscountAmount = array_sum(array_column($pastPayrollSheets->past_payroll_sheets, 'tax_discount_amount'));
        $pastSstAmount = array_sum(array_column($pastPayrollSheets->past_payroll_sheets, 'sst_amount'));
        $pastTaxAmount = array_sum(array_column($pastPayrollSheets->past_payroll_sheets, 'tax_amount'));
        $pastMonthCount = $pastPayrollSheets->past_month_count;
        $futureMonthCount = 12 - $pastMonthCount;

        $totalTaxLiability -= $pastTaxLiabilityAmount;
        $totalTaxDiscountAmount -= $pastTaxDiscountAmount;
        $totalSstAmount -= $pastSstAmount;
        $totalTaxAmount -= $pastTaxAmount;

        $sstAmount = $totalSstAmount ? round($totalSstAmount / $futureMonthCount, 2) : 0;
        $taxAmount = $totalTaxAmount ? round($totalTaxAmount / $futureMonthCount, 2) : 0;
        $taxLiability = $totalTaxLiability ? round($totalTaxLiability / $futureMonthCount, 2) : 0;
        $taxDiscountAmount = $totalTaxDiscountAmount ? round($totalTaxDiscountAmount / $futureMonthCount, 2) : 0;

        return json_encode([
            'total_tax_liability' => $totalTaxLiability,
            'total_tax_discount' => $totalTaxDiscountAmount,
            'total_tax_amount' => $totalTaxAmount,
            'total_sst_amount' => $totalSstAmount,
            'sst_amount' => $sstAmount,
            'tax_liability' => $taxLiability,
            'tax_amount' => $taxAmount,
            'tax_discount_amount' => $taxDiscountAmount,
        ]);
    }

    public function createPayrollSheet($inputs, $paymentMaster)
    {
        $employee = $this->employees->find($inputs['employee_id']);
        $payrollBatch = $this->payrollBatches->find($inputs['payroll_batch_id']);
        unset($inputs['fiscal_year_id']);
        $payrollSheet = $this->model->firstOrCreate($inputs);
        $year = date('Y', strtotime($payrollBatch->fiscalyear->start_date));
        $startDate = date($year ) .'-'. $payrollBatch->month.'-01';
        $married = ucwords($employee->maritalStatus->title) == 'Married' ? 1 : 0;
        $disabled = $employee->finance ?->disabled ? $employee->finance->disabled : 0;

        $payrollSheet->update([
            'department_id'=>$employee->department_id,
            'designation_id'=>$employee->designation_id,
            'start_date'=>$startDate,
            'end_date'=>$startDate,
            'married'=>$married,
            'disabled'=>$disabled,
            'remote_category'=>$employee->finance ?->remote_category,
        ]);
        $payrollSheet->details()->delete();

        $this->createPayrollSheetBenefits($payrollSheet, $paymentMaster);
        $this->createPayrollSheetDeductions($payrollSheet, $paymentMaster);

        $pastPayrollSheets = json_decode($this->getPastPayrollSheets($payrollSheet));
        $grossBenefitAmount = $this->calculateMonthlyBenefitAmount($payrollSheet, $paymentMaster);
        $annualBenefitAmount = $this->calculateAnnualBenefitAmount($payrollSheet, $pastPayrollSheets, $paymentMaster);
        $grossDeductionAmount = $this->calculateMonthlyDeductionAmount($payrollSheet, $paymentMaster);
        $annualDeductionAmounts = json_decode($this->calculateAnnualDeductionAmount($payrollSheet, $pastPayrollSheets));
        $annualNonTaxableDeductionAmount = $annualDeductionAmounts->annualNonTaxableDeductionAmount;
        $taxableAmount = $annualBenefitAmount - $annualNonTaxableDeductionAmount;

        $response = json_decode($this->calculateTaxAmounts($taxableAmount, $payrollSheet, $pastPayrollSheets));

        $inputs['department_id'] = $employee->department_id;
        $inputs['designation_id'] = $employee->designation_id;
        $inputs['married'] = ucwords($employee->maritalStatus->title) == 'Married' ? 1 : 0;
        $inputs['gross_amount'] = $grossBenefitAmount;
        $inputs['total_deduction_amount'] = $grossDeductionAmount;
        $inputs['sst_amount'] = $response->sst_amount;
        $inputs['tax_liability'] = $response->tax_liability;
        $inputs['tax_discount_amount'] = $response->tax_discount_amount;
        $inputs['tax_amount'] = $response->tax_amount;
        $inputs['net_amount'] = $grossBenefitAmount - $inputs['tax_amount'] - $grossDeductionAmount;
        if($employee->finance->created_at){
            $inputs['disabled'] = $employee->finance->disabled;
            $inputs['remote_category'] = $employee->finance->remote_category;
        }

        $payrollSheet->fill($inputs)->save();
    }

    protected function createPayrollSheetBenefits($payrollSheet, $paymentMaster)
    {
        $benefits = $paymentMaster->paymentDetails->filter(function ($master) {
            return $master->paymentItem->type == 'B' && $master->paymentItem->frequency == 12;
        });
        foreach($benefits as $benefit)
        {
            $payrollSheet->details()->create([
                'payment_item_id'=>$benefit->payment_item_id,
                'amount'=>$benefit->amount,
            ]);
        }
        return true;
    }

    protected function createPayrollSheetDeductions($payrollSheet, $paymentMaster)
    {
        $deductions = $paymentMaster->paymentDetails->filter(function ($master) {
            return $master->paymentItem->type == 'D';
        });

        foreach($deductions as $deducation)
        {
            $payrollSheet->details()->create([
                'payment_item_id'=>$deducation->payment_item_id,
                'amount'=>$deducation->amount,
            ]);
        }
        return true;
    }

    protected function getPastPayrollSheets($payrollSheet)
    {
        $employee = $payrollSheet->employee;
        $pastPayrollBatchIds = $this->payrollBatches->select(['*'])
            ->where('payroll_fiscal_year_id', $payrollSheet->payrollBatch->payroll_fiscal_year_id)
            ->where('month', '<>', $payrollSheet->payrollBatch->month)
            ->pluck('id')->toArray();
        $pastPayrollSheets = $this->model->select(['*'])
            ->whereIn('payroll_batch_id', $pastPayrollBatchIds)
            ->where('employee_id', $employee->id)
            ->get();

        return json_encode([
            'past_payroll_sheets' => $pastPayrollSheets,
            'past_month_count' => count($pastPayrollBatchIds),
        ]);
    }
}
