<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\event_types\CreateEventTypeRequest;
use App\Http\Requests\event_types\UpdateEventTypeRequest;
use App\Models\EventType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class EventTypeController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $events = EventType::orderBy('created_at', 'desc')->get();
                return DataTables::of($events)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($event) {
                        $action = Auth::user()->hasPermissionTo('update_event_type') ? ('<a href="#" data-id=' . $event->id . ' class="update-event-type btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>') : null;
                        $action .= Auth::user()->hasPermissionTo('delete_event_type') ? ('<a href="#" data-url="' . route('events.types.delete', $event->id) . '" class="delete-event-type btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>') : null;
                        return $action;
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
