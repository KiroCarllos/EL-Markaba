<?php

namespace App\Repositories;

use App\Http\Resources\StudentSuggestionResource;
use App\Models\User;

class StudentRepository
{
    public function search($filterValue)
    {
//        $results = User::student()
////            ->where(function ($query) use ($filterValue) {
////                $query->searchSuggestion($filterValue) // Applying scope to the main query
////                ->whereHas('student_details', function ($subquery) use ($filterValue) {
////                    $subquery->where("area_id", auth()->user()->father_details->area_id);
//////                        ->searchSuggestion($filterValue);
////                });
////            })
////            ->orWhere(function ($query) use ($filterValue) {
////                $query->searchSuggestion($filterValue) // Applying scope to the main query
////                ->where("created_by", auth("api")->id());
//////                    ->whereHas('student_details', function ($subquery) use ($filterValue) {
//////                        $subquery->searchSuggestion($filterValue);
//////                    });
////            })
////            ->with("student_details")
////            ->paginate(5);
////
////        return $results;
        $results = User::student()
            ->where(function ($query) use ($filterValue) {
                if ($filterValue) {
                    $query->searchSuggestion($filterValue)
                        ->orWhereHas('student_details', function ($subquery) use ($filterValue) {
                            $subquery->where("area_id", auth()->user()->father_details->area_id);
                        });
                } else {
                    $query->whereHas('student_details', function ($subquery) {
                        $subquery->where("area_id", auth()->user()->father_details->area_id);
                    });
                }
            })
            ->orWhere(function ($query) use ($filterValue) {
                if ($filterValue) {
                    $query->searchSuggestion($filterValue)
                        ->where("created_by", auth("api")->id());
                } else {
                    $query->where("created_by", auth("api")->id());
                }
            })
            ->with("student_details")
            ->paginate(5);
        return $results;
    }
}
