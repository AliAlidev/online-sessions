<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\event_types\CreateEventTypeRequest;
use App\Http\Requests\event_types\UpdateEventTypeRequest;
use App\Models\EventType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EventTypeController extends Controller
{
    function index(Request $request)
    {
        if ($request->ajax()) {
            $events = EventType::get();
            return DataTables::of($events)
                ->addIndexColumn()
                ->addColumn('actions', function ($event) {
                    return '<a href="#" data-id=' . $event->id . ' class="update-event-type"><i class="bx bx-edit-alt me-1" style="color:gray"></i></a>
                            <a href="#" data-url="' . route('events.types.delete', $event->id) . '" class="delete-event-type"><i class="bx bx-trash me-1" style="color:red"></i> </a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('dashboard.event_types.index');
    }

    function show($id)
    {
        $type = EventType::find($id);
        return response()->json(['success' => true, 'data' => $type]);
    }

    function store(CreateEventTypeRequest $request)
    {
        $data = $request->validated();
        EventType::create($data);
        return response()->json(['success' => true, 'message' => 'Event type has been created successfully']);
    }

    function update(UpdateEventTypeRequest $request)
    {
        $data = $request->validated();
        EventType::find($data['event_type_id'])->update([
            'name' => $data['name']
        ]);
        return response()->json(['success' => true, 'message' => 'Event type has been updated successfully']);
    }

    function delete($id)
    {
        EventType::find($id)->delete();
        return response()->json(['success' => true, 'message' => 'Event type has been deleted successfully']);
    }

}
