<?php

namespace Dashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the profile for a given user.
     */
    public function index(): View
    {
        return view('dashboard::pages.index');
    }
}
