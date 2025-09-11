<?php

namespace Modules\Supplier\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;
use Modules\Supplier\Models\Supplier;

class SupplierExport implements FromCollection, Responsable, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'suppliers.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    private $counter = 1;

    /**
     * Adding a heading row
     */
    public function headings(): array
    {
        return [
            'S.N.',
            'Supplier Type',
            'Supplier Name',
            'VAT/PAN Number',
            'Contact Number',
            'Email Address',
            'Contact Person Name',
            'Contact Person Email Address',
            'Address 1',
            'Address 2',
            'Account Number',
            'Account Name',
            'Bank',
            'Branch',
        ];
    }

    public function map($record): array
    {
        return [
            $this->counter++,
            $record->getSupplierType(),
            $record->supplier_name,
            $record->vat_pan_number,
            $record->contact_number,
            $record->email_address,
            $record->contact_person_name,
            $record->contact_person_email_address,
            $record->address1,
            $record->address2,
            $record->account_number,
            $record->account_name,
            $record->bank_name,
            $record->branch_name,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $record = Supplier::select([
            'supplier_type',
            'supplier_name',
            'vat_pan_number',
            'contact_number',
            'email_address',
            'contact_person_name',
            'contact_person_email_address',
            'address1',
            'address2',
            'account_number',
            'account_name',
            'bank_name',
            'branch_name',
        ])->get();

        return $record;
    }
}
