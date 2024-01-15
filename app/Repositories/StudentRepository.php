<?php

namespace App\Repositories;

use App\Http\Resources\StudentSuggestionResource;
use App\Models\User;

class StudentRepository
{
    public function search($filterValue)
    {
        $results = User::student()
            ->active()
            ->where(function ($query) {
                $query->where("created_by", auth("api")->id())
                    ->orWhereHas('student_details', function ($subquery) {
                        $subquery->where("area_id", auth()->user()->father_details->area_id);
                    });
            })
//            ->where(function ($query) use ($filterValue) {
//                $query->SearchSuggestion($filterValue);
//            })
            ->with("student_details")
            ->paginate(5);
        return $results;
    }
}
