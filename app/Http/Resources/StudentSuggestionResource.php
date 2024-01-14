<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentSuggestionResource extends JsonResource
{
    public function toArray($request)
    {
        $high = [
            "faculty" => $this->student_details->faculty->name_ar ?? null,
            "university" => $this->student_details->faculty->university->name_ar ?? null,
        ];
        $else = [
            "education" => $this->student_details->else_education ?? null,
        ];
        return [
            "id" => $this->id,
            "name" => $this->name,
            "mobile" => $this->mobile,
            "email" => $this->email,
            "image" => $this->image,
            "age" => $this->age,
            "education_level" => $this->student_details->education ?? null,
            "high" => $high,
            "else" => $else,
            "national_id" => $this->student_details->national_id ?? null,
            "major" => $this->student_details->major ?? null,
            "gender" => $this->student_details->gender ?? null,
            "area" => $this->student_details->area->name_ar ?? null,
            "address" => $this->student_details->address ?? null,
            "courses" => $this->student_details->courses ?? null,
            "prior_experiences" => $this->student_details->prior_experiences ?? null,
        ];


    }
}
