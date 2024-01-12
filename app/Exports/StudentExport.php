<?php

namespace App\Exports;


use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentExport implements FromQuery, WithMapping, WithHeadings,WithCustomCsvSettings
{
    public function map($student): array
    {
        $prior_experiences = implode(', ', $student->student_details->prior_experiences); // Replace 'yourArrayAttribute' with the actual attribute containing the array
        $courses = implode(', ', $student->student_details->courses); // Replace 'yourArrayAttribute' with the actual attribute containing the array

        return [
            $student->id,
            $student->name,
            $student->mobile,
            $student->status,
            $student->student_details->gender,
            $student->student_details->national_id,
            $student->student_details->education == "high" ? "عالي" : "متوسط",
            $student->student_details->education == "high" ? $student->student_details->faculty->name_en : "",
            $student->student_details->education == "high" ? $student->student_details->faculty->university->name_en : "",
            $student->student_details->major,
            $student->student_details->graduated_at,
            $courses,
            $prior_experiences,
            $student->student_details->address,
        ];
    }

    public function headings(): array
    {
        return ['id', 'الاسم', 'الموبيل', 'حالة الحساب', 'الجنس', 'رقم القومي', 'مستوي التعليم', 'الجامعه', 'الكليه', 'التخصص', 'سنة التخرج', 'الكورسات', 'الخبره', 'العنوان'];
    }

    public function query()
    {
        return \App\Models\User::Student()->whereHas("student_details")->latest();
    }
    public function getCsvSettings(): array
    {
        return [

            'use_bom' => true,
        ];
    }
}
