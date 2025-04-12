<?php

namespace App\Http\Controllers\landing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandingPageEventController extends Controller
{
    function index($year,$month,$customer)
    {
        dd($year,$month,$customer);
        return view('dashboard.event.create');
    }
}
