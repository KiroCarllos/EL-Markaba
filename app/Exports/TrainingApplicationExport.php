<?php

namespace App\Exports;


use App\Models\JobApplication;
use App\Models\TrainingApplication;
use App\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TrainingApplicationExport implements FromQuery, WithMapping, WithHeadings,WithCustomCsvSettings
{
    protected $trainingId;
    public function __construct($trainingId)
    {
        $this->trainingId = $trainingId;
    }

    public function map($training): array
    {
        return [
            $training->id,
            $training->status,
            $training->receipt_image,
            $training->user->name,
            $training->user->mobile,
            $training->user->email,
            $training->training->status,
            $training->training->paid == "yes" ?"مدفوع":"مجاني",
            $training->training->title_ar,

        ];
    }

    public function headings(): array
    {

        return ['id',"حالة الطلب","صورة ايصال الدفع","اسم متقدم التدريب","موبيل متقدم التدريب","ايميل متقدم التدريب",
             'حالة التدريب','نوع التدريب', 'عنوان التدريب ',
        ];
    }

    public function query()
    {
        return TrainingApplication::where("training_id",$this->trainingId)->whereHas("user");
    }
    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
}
