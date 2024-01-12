<?php

namespace App\Exports;


use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyExport implements FromQuery, WithMapping, WithHeadings,WithCustomCsvSettings
{
    public function map($student): array
    {

        return [
            $student->id,
            $student->name,
            $student->mobile,
            $student->status,
        ];
    }

    public function headings(): array
    {
        return ['id', 'الاسم', 'الموبيل', 'حالة الحساب'];
    }

    public function query()
    {
        return \App\Models\User::company()->latest();
    }
    public function getCsvSettings(): array
    {
        return [

            'use_bom' => true,
        ];
    }
}
