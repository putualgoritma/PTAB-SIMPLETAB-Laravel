<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsenceExport implements FromCollection, WithHeadings
{
    use Exportable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'Emp No',
            'AC-No',
            'No',
            'Name',
            'Auto-Asign',
            'Date',
            'TimeTable',
            'On_Duty',
            'Off_Duty',
            'Clock_in',
            'Clock_out',
            'Keterangan',
            'Memo'
        ];
    }
}
