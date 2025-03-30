<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    function index(Request $request)
    {
        $year = $request->route('year');
        $month = $request->route('month');
        $customer = $request->route('customer');
        $event = Event::where('bunny_event_name', $customer)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.index', ['year' => $year, 'month' => $month, 'customer' => $customer, 'event' => $event]);
    }

    function gallery(Request $request)
    {
        $year = $request->route('year');
        $month = $request->route('month');
        $customer = $request->route('customer');
        $event = Event::where('bunny_event_name', $customer)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.gallery.gallery_layout', ['year' => $year, 'month' => $month, 'customer' => $customer, 'event' => $event]);
    }

    function image(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        $customer = $request->get('customer');
        $event = Event::where('bunny_event_name', $customer)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.gallery.gallery_image', ['year' => $year, 'month' => $month, 'customer' => $customer, 'event' => $event]);
    }

    function share(Request $request)
    {
        $year = $request->route('year');
        $month = $request->route('month');
        $customer = $request->route('customer');
        $event = Event::where('bunny_event_name', $customer)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.share', ['year' => $year, 'month' => $month, 'customer' => $customer, 'event' => $event]);
    }

    function video(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        $customer = $request->get('customer');
        $event = Event::where('bunny_event_name', $customer)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.gallery.gallery_video', ['year' => $year, 'month' => $month, 'customer' => $customer, 'event' => $event]);
    }
}
