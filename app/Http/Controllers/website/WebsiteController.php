<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventFolder;
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
        return view('website.pages.gallery.gallery', ['year' => $year, 'month' => $month, 'customer' => $customer, 'event' => $event]);
    }

    function image(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        $customer = $request->get('customer');
        $folderId = $request->get('folderId');
        $event = Event::where('bunny_event_name', $customer)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        $folder = EventFolder::find($folderId);
        $images = $folder->files;
        return view('website.pages.gallery.image', ['year' => $year, 'month' => $month, 'customer' => $customer, 'event' => $event, 'images' => $images]);
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
        $folderId = $request->get('folderId');
        $event = Event::where('bunny_event_name', $customer)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        $folder = EventFolder::find($folderId);
        $videos = $folder->files;
        return view('website.pages.gallery.video', ['year' => $year, 'month' => $month, 'customer' => $customer, 'event' => $event, 'videos' => $videos]);
    }
}
