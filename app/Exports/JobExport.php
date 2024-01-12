<?php

namespace App\Exports;


use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobExport implements FromQuery, WithMapping, WithHeadings,WithCustomCsvSettings
{
    public function map($job): array
    {

        return [
            $job->id,
            $job->company->name,
            $job->company->mobile,
            $job->company->email,
            $job->status,
            $job->title_en,
            $job->title_ar,
            $job->description_en,
            $job->description_ar,
            $job->work_type,
            $job->job_type,
            $job->work_hours,
            $job->contact_email,
            $job->address,
            $job->location,
            $job->expected_salary_from,
            $job->expected_salary_to,

        ];
    }

    public function headings(): array
    {
        return ['id', 'اسم الشركة', 'موبيل الشركة', 'ايميل الشركة', 'حالة الوظيفة','عنوان الوظيفة بالانجليزيه', 'عنوان الوظيفة بالعربية', 'الوصف الوظيفة بالانجليزيه', 'الوصف الوظيفة بالعربية',
            'نوع الدوام', 'حالة العمل', 'سعات العمل', 'ايميل التواصل', 'العنوان', 'اللوكيشن', 'الراتب المتوقع من', 'الراتب المتوقع الي'];
    }

    public function query()
    {
        return \App\Models\Job::whereHas("company")->latest();
    }
    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
}
