<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeptHeadController extends Controller
{
    public function dashboard()
    {
        return view('dept_head.dashboard');
    }
}