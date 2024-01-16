<?php

namespace App\Repositories;

use App\Http\Resources\StudentSuggestionResource;
use App\Models\User;

class StudentRepository
{
    public function search($filterValue)
    {
        $results = User::student()
            ->where("created_by", auth("api")->id())
            ->orWhereHas("student_details", function ($q) use ($filterValue) {
                $q->SearchSuggestion($filterValue)->where("area_id", auth()->user()->father_details->area_id);
            })
            ->with("student_details")
            ->paginate(5);
        return $results;
    }
}
