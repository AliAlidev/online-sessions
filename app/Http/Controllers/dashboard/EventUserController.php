<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\events_users\CreateEventUserRequest;
use App\Http\Requests\events_users\UpdateEventUserRequest;
use App\Models\Event;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EventUserController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $eventUsers = User::where('dashboard_user', 1)->where('user_type', 'event-user')->orderBy('created_at', 'desc')->get();
                return DataTables::of($eventUsers)
                    ->addColumn('actions', function ($client) {
                        return '<a data-id="' . $client->id . '" href="' . route('events.users.edit', $client->id) . '" class="update-client btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>
                                <a href="#" data-url="' . route('events.users.delete', $client->id) . '" class="delete-user btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>';
                    })
                    ->addIndexColumn()
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('dashboard.event_user.index');
        } catch (Exception $th) {
            createServerError($th, "indexUser", "users");
            return false;
        }
    }

    function create()
    {
        try {
            $events = Event::pluck('event_name', 'id')->toArray();
            return view('dashboard.event_user.create', ['events' => $events]);
        } catch (Exception $th) {
            createServerError($th, "createUser", "users");
            return false;
        }
    }

    function store(CreateEventUserRequest $request)
    {
        try {
            $data = $request->validated();
            unset($data['permissions']);
            unset($data['password_confirmation']);
            $data['dashboard_user'] = 1;
            $user = User::create($data);
            $this->eventUserAssignPermissions($user);
            session()->flash('success', 'User has been created successfully');
            return response()->json(['success' => true, 'url' => route('events.users.index')]);
        } catch (Exception $th) {
            createServerError($th, "storeUser", "users");
            return false;
        }
    }

    function eventUserAssignPermissions($user)
    {
        $permissions = [
            'list_events',
            'list_folders',
            'upload_image',
            'approve_decline_image',
            'upload_video',
            'approve_decline_video',
            'delete_image',
            'delete_video',
            'update_image',
            'update_video'
        ];
        $user->givePermissionTo($permissions);
    }

    function edit($id)
    {
        try {
            $user = User::find($id);
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            return view('dashboard.event_user.update', ['permissions' => $permissions, 'user' => $user]);
        } catch (Exception $th) {
            createServerError($th, "editUser", "users");
            return false;
        }
    }

    function update(UpdateEventUserRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $data = array_filter($data, fn($value) => !is_null($value));
            $user = User::find($id);
            unset($data['permissions']);
            unset($data['password_confirmation']);
            unset($data['user_id']);
            $user->update($data);
            session()->flash('success', 'User has been updated successfully');
            return response()->json(['success' => true, 'url' => route('events.users.edit', $id)]);
        } catch (Exception $th) {
            createServerError($th, "updateUser", "users");
            return false;
        }
    }

    function delete($id)
    {
        try {
            User::find($id)->delete();
            session()->flash('success', 'User has been deleted successfully');
            return response()->json(['success' => true]);
        } catch (Exception $th) {
            createServerError($th, "deleteUser", "users");
            return false;
        }
    }
}
