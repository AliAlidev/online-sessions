<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\users\CreateUserRequest;
use App\Http\Requests\users\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $clients = User::get();
                return DataTables::of($clients)
                    ->addColumn('actions', function ($client) {
                        return '<a data-id="' . $client->id . '" href="' . route('users.edit', $client->id) . '" class="update-client btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>
                                <a href="#" data-url="' . route('users.delete', $client->id) . '" class="delete-user btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>';
                    })
                    ->addColumn('permissions', function ($user) {
                        $permissions = '';
                        foreach ($user->getAllPermissions() as $key => $permission) {
                            if ($key % 3 == 0)
                                $permissions .= '<br/>';
                            $permissions .= '<small class="badge bg-label-primary me-1 m-1">' . ucwords(str_replace('_', ' ', $permission->name)) . '</small>';
                        }
                        return $permissions;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['permissions', 'actions'])
                    ->make(true);
            }
            return view('dashboard.user.index');
        } catch (Exception $th) {
            createServerError($th, "indexUser", "users");
            return false;
        }
    }

    function create()
    {
        try {
            return view('dashboard.user.create');
        } catch (Exception $th) {
            createServerError($th, "createUser", "users");
            return false;
        }
    }

    function edit($id)
    {
        try {
            $user = User::find($id);
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            return view('dashboard.user.update', ['permissions' => $permissions, 'user' => $user]);
        } catch (Exception $th) {
            createServerError($th, "editUser", "users");
            return false;
        }
    }

    function store(CreateUserRequest $request)
    {
        try {
            $data = $request->validated();
            $permissions = $data['permissions'];
            unset($data['permissions']);
            unset($data['password_confirmation']);
            $user = User::create($data);
            $user->givePermissionTo($permissions);
            session()->flash('success', 'User has been created successfully');
            return response()->json(['success' => true, 'url' => route('users.index')]);
        } catch (Exception $th) {
            createServerError($th, "storeUser", "users");
            return false;
        }
    }

    function update(UpdateUserRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $user = User::find($id);
            $permissions = $data['permissions'];
            unset($data['permissions']);
            unset($data['password_confirmation']);
            unset($data['user_id']);
            $user->update($data);
            $user->syncPermissions($permissions);
            session()->flash('success', 'User has been updated successfully');
            return response()->json(['success' => true, 'url' => route('users.index')]);
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
