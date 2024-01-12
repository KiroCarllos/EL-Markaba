<?php

namespace App\Exports;


use App\Models\JobApplication;
use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobApplicationExport implements FromQuery, WithMapping, WithHeadings,WithCustomCsvSettings
{
    protected $jobId;
    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    public function map($application): array
    {
        return [
            $application->id,
            $application->status,
            $application->user->name,
            $application->user->mobile,
            $application->user->email,
            $application->job->company->name,
            $application->job->company->mobile,
            $application->job->company->email,
            $application->job->status,
            $application->job->title_en,
            $application->job->title_ar,
            $application->job->description_en,
            $application->job->description_ar,
            $application->job->work_type,
            $application->job->job_type,
            $application->job->work_hours,
            $application->job->contact_email,
            $application->job->address,
            $application->job->location,
            $application->job->expected_salary_from,
            $application->job->expected_salary_to,

        ];
    }

    public function headings(): array
    {

        return ['id',"حالة الطلب","اسم متقدم الوظيفة","موبيل متقدم الوظيفة","ايميل متقدم الوظيفة", 'اسم الشركة', 'موبيل الشركة', 'ايميل الشركة', 'حالة الوظيفة','عنوان الوظيفة بالانجليزيه', 'عنوان الوظيفة بالعربية', 'الوصف الوظيفة بالانجليزيه', 'الوصف الوظيفة بالعربية',
            'نوع الدوام', 'حالة العمل', 'سعات العمل', 'ايميل التواصل', 'العنوان', 'اللوكيشن', 'الراتب المتوقع من', 'الراتب المتوقع الي'];
    }

    public function query()
    {
        return JobApplication::where("job_id",$this->jobId)->whereHas("user");
    }
    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
}
