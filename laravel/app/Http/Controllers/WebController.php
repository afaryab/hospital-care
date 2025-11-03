<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class WebController extends Controller
{
    public function index()
    {
        return Inertia::render('dashboard');
    }

    public function counter()
    {
        return Inertia::render('counter');
    }

    public function hospitalSettings()
    {
        return Inertia::render('admin/hospital-settings');
    }
}
