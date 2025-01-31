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
                ->editColumn('qr_code', function ($row) {
                    return '<img src="/'.$row->qr_code.'" alt="" width="100px" height="100px">';
                })
                ->addColumn('actions', function ($event) {
                    return '<a href="' . route('events.edit', $event->id) . '" class="update-event"><i class="bx bx-edit-alt me-1" style="color:gray"></i></a>
                            <a href="#" data-url="' . route('events.delete', $event->id) . '" class="delete-event"><i class="bx bx-trash me-1" style="color:red"></i> </a>';
                })
                ->addIndexColumn()
                ->rawColumns(['qr_code','actions'])
                ->make(true);
        }
        return view('dashboard.event.index');
    }

    function create()
    {
        return view('dashboard.event.create');
    }

    function store(CreateEventRequest $request)
    {
        $data = $request->validated();
        $data['cover_image'] = $request->hasFile('cover_image') ? 'storage/' . uploadFile($request->file('cover_image'), 'event_cover_image') : null;
        $data['profile_picture'] = $request->hasFile('profile_picture') ? 'storage/' . uploadFile($request->file('profile_picture'), 'profile_picture') : null;
        $data['qr_code'] = 'storage/' . uploadBase64File($data['qr_code'], 'event_qr_code');
        Event::create([
            'event_name' => $data['event_name'],
            'cover_image' => $data['cover_image'],
            'event_type' => $data['event_type'],
            'profile_picture' => $data['profile_picture'],
            'client_id' => $data['client_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'customer' => $data['customer'],
            'venue' => $data['venue'],
            'active_duration' => $data['active_duration'],
            'description' => $data['description'],
            'event_link' => $data['event_link'],
            'event_password' => $data['event_password'],
            'welcome_message' => $data['welcome_message'],
            'qr_code' => $data['qr_code'],
        ]);
        session()->flash('success', 'Event has been created successfully');
        return response()->json(['success' => true, 'url' => route('events.index')]);
    }

    function edit($id)
    {
        $event = Event::find($id);
        return view('dashboard.event.update', compact('event'))->render();
    }

    function update(Request $request, $id)
    {
        $event = Event::find($id);
        dd($event);
    }

    function delete($id)
    {
        Event::find($id)->delete();
        $count = Event::count();
        return response()->json(['success' => true, 'count' => $count]);
    }
}
