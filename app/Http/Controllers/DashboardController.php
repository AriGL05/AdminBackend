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

    public function aboutFilm()
    {
        return view('films/about_film');
    }

    public function contact()
    {
        return view('contact');
    }

    public function submitContact(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Here you can add logic to send emails or store contact messages
        // For now, just redirect with a success message

        return redirect()->back()->with('success', 'Your message has been sent successfully!');
    }
}
