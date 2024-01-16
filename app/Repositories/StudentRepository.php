<?php

namespace App\Repositories;

use App\Http\Resources\StudentSuggestionResource;
use App\Models\User;

class StudentRepository
{
    public function search($filterValue)
    {
        $userId = auth("api")->id();
        $userAreaId = auth()->user()->father_details->area_id;

        $results = User::student()
            ->where("created_by", $userId)
            ->orWhereHas('student_details', function ($subquery) use ($filterValue, $userAreaId) {
                $subquery->where("area_id", $userAreaId)
                    ->searchSuggestion($filterValue);
            })
            ->with("student_details")
            ->paginate(5);

        return $results;
    }
}
