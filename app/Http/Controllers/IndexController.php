<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;    // talan nem kell
use App\Http\Controllers\Controller as Controller;

class IndexController extends Controller
{
    public function index(){
        return view("index");
    }
}
