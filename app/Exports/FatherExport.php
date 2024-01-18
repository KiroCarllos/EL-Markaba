<?php

namespace App\Exports;


use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FatherExport implements FromQuery, WithMapping, WithHeadings,WithCustomCsvSettings
{
    public function map($father): array
    {
        return [
            $father->id,
            $father->name,
            $father->mobile,
            $father->email,
            $father->status,
            $father->father_details->area->name_ar,
        ];
    }

    public function headings(): array
    {
        return ['id', 'الاسم', 'الموبيل', 'الايميل', 'حالة الحساب','المنطقة'];
    }

    public function query()
    {
        return \App\Models\User::Father()->whereHas("father_details")->latest();
    }
    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
}
