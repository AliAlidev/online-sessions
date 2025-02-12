<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\event_types\CreateEventTypeRequest;
use App\Http\Requests\event_types\UpdateEventTypeRequest;
use App\Models\EventType;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EventTypeController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $events = EventType::get();
                return DataTables::of($events)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($event) {
                        return '<a href="#" data-id=' . $event->id . ' class="update-event-type btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>
                                <a href="#" data-url="' . route('events.types.delete', $event->id) . '" class="delete-event-type btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('dashboard.event_types.index');
        } catch (Exception $th) {
            createServerError($th, "indexEventType", "eventTypes");
            return false;
        }
    }

    function show($id)
    {
        try {
            $type = EventType::find($id);
            return response()->json(['success' => true, 'data' => $type]);
        } catch (Exception $th) {
            createServerError($th, "showEventType", "eventTypes");
            return false;
        }
    }

    function store(CreateEventTypeRequest $request)
    {
        try {
            $data = $request->validated();
            EventType::create($data);
            return response()->json(['success' => true, 'message' => 'Event type has been created successfully']);
        } catch (Exception $th) {
            createServerError($th, "storeEventType", "eventTypes");
            return false;
        }
    }

    function update(UpdateEventTypeRequest $request)
    {
        try {
            $data = $request->validated();
            EventType::find($data['event_type_id'])->update([
                'name' => $data['name']
            ]);
            return response()->json(['success' => true, 'message' => 'Event type has been updated successfully']);
        } catch (Exception $th) {
            createServerError($th, "updateEventType", "eventTypes");
            return false;
        }
    }

    function delete($id)
    {
        try {
            EventType::find($id)->delete();
            return response()->json(['success' => true, 'message' => 'Event type has been deleted successfully']);
        } catch (Exception $th) {
            createServerError($th, "deleteEventType", "eventTypes");
            return false;
        }
    }
}
