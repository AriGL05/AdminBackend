<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard');
    }
    public function tablas()
    {
        return view('tablas');
    }

    public function newFilm()
    {
        return view('films/new_film');
    }
}
