<?php

namespace App\Http\Controllers\Dashboard;


use App\Http\Controllers\Controller;
use App\Models\User;

class WelcomeController extends Controller
{
    public function index()
    {
        $users_count = User::whereRoleIs('admin')->count();
        return view('dashboard.welcome', compact(   'users_count'));
    }//end of index

}//end of controller
