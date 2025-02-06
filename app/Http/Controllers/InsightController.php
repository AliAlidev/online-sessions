<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsightController extends Controller
{
    function index()
    {
        $events = getEventsIncreasingCount();
        $clients = getClientsIncreasingCount();
        return view('dashboard.insight.index', [
            'events_percentage_value' => $events['percentage'],
            'events_percentage_color' => $events['color'],
            'events_percentage_sign' => $events['sign'],
            'clients_percentage_value' => $clients['percentage'],
            'clients_percentage_color' => $clients['color'],
            'clients_percentage_sign' => $clients['sign']
        ]);
    }
}
