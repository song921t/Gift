<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class UsersController extends Controller
{
    //
    public function create(){
        //echo 1;die;
        return view('users.create');
    }
}
