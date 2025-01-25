<?php
namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromCollection;

class IncomeExport implements FromCollection
{
    public function collection()
    {
        return Income::all();
    }
}
