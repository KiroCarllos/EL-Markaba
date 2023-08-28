<?php

namespace App\Http\Controllers\Dashboard;


use App\Http\Controllers\Controller;
use App\Models\User;

class WelcomeController extends Controller
{
    public function index()
    {
//        30005210201021
//        300005210201021
//        30012040201451
//        calculateAgeFromNationalId(300005210201021);


        $users_count = User::whereRoleIs('admin')->count();
        return view('dashboard.welcome', compact(   'users_count'));
    }//end of index

}//end of controller
