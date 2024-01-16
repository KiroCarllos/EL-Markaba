<?php

namespace App\Repositories;

use App\Http\Resources\StudentSuggestionResource;
use App\Models\User;

class StudentRepository
{
    public function search($filterValue)
    {
        $results = User::student()
            ->where(function ($query) use ($filterValue) {
                $query->whereHas('student_details', function ($subquery) use ($filterValue){
                        $subquery->where("area_id", auth()->user()->father_details->area_id)->SearchSuggestion($filterValue);
                    });
            })
            ->orWhere("created_by",auth("api")->id())
            ->with("student_details")
            ->paginate(5);
        return $results;
    }
}
