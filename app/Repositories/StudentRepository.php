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
                $query->SearchSuggestion($filterValue)
                    ->orWhereHas('student_details', function ($subquery) use ($filterValue) {
                    $subquery->where("area_id", auth()->user()->father_details->area_id)
                        ->searchSuggestion($filterValue);
                });
            })
            ->orWhere(function ($query) use ($filterValue){
                $query->SearchSuggestion($filterValue)->where("created_by", auth("api")->id())
                    ->whereHas('student_details', function ($subquery) use ($filterValue) {
                        $subquery->searchSuggestion($filterValue);
                    });
            })


            ->paginate(5);

        return $results;
    }
}
