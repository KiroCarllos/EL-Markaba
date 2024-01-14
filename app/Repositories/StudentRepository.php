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
            ->where(function ($query) use ($filterValue) {
                $query->SearchSuggestion($filterValue)
                    ->orWhereHas('student_details', function ($subquery) use ($filterValue) {
                        $subquery->SearchSuggestion($filterValue);
                    });
            })
            ->with("student_details")
            ->paginate(5);
        return $results;
    }
}
