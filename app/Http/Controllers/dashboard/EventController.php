<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\events\CreateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EventController extends Controller
{
    function index(Request $request)
    {
        if ($request->ajax()) {
            $events = Event::get();
            return DataTables::of($events)
                ->addColumn('actions', function ($event) {
                    return '<a href="' . route('events.edit', $event->id) . '" class="btn btn-primary update-event">Edit</a>
                            <a href="' . route('events.delete', $event->id) . '" class="btn btn-danger delete-event">Delete</a>';
                })
                ->addIndexColumn()
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('dashboard.event.index');
    }

    function create()
    {
        return view('dashboard.event.create');
    }

    function store(CreateEventRequest $request) {
        dd($request->validated());
       $url = uploadBase64File($request->all()['event_qr_code'], 'event_qr_code');
        dd($url);
    }

    function edit($id)
    {
        $event = Event::find($id);
        return view('dashboard.event.update', compact('event'))->render();
    }

    function update(Request $request ,$id)
    {
        $event = Event::find($id);
        dd($event);
    }
}
