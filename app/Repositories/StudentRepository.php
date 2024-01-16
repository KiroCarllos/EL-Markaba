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
        dd($userAreaId);
        $results = User::student()
            ->where(function ($query) use ($userId, $userAreaId, $filterValue) {
                $query->where("created_by", $userId)
                    ->orWhereHas('student_details', function ($subquery) use ($userAreaId, $filterValue) {
                        $subquery->where("area_id", $userAreaId)
                            ->searchSuggestion($filterValue);
                    });
            })
            ->with("student_details")
            ->paginate(5);

        return $results;
    }

}
