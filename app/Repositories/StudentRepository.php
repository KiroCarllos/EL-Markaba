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
            ->where("created_by", auth("api")->id())
            ->orWhere(function ($query) use ($filterValue) {
                $query->SearchSuggestion($filterValue)
                    ->orWhereHas('student_details', function ($subquery) use ($filterValue) {
                        $subquery->SearchSuggestion($filterValue);
                    });
            })
            ->orWhere(function ($query) {
                $query->WhereHas('student_details', function ($subquery) {
                        $subquery->where("area_id", auth()->user()->father_details->area_id);
                    });
            })
            ->with("student_details")
            ->paginate(5);
        return $results;
    }
}
