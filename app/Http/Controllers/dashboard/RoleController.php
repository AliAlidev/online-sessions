<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\roles\CreateRoleRequest;
use App\Http\Requests\roles\UpdateRoleRequest;
use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $events = Role::whereNotIn('name', ['super-admin'])->get();
                return DataTables::of($events)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($role) {
                        return '<a href="#" data-id=' . $role->id . ' class="update-role btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>
                                <a href="#" data-url="' . route('roles.delete', $role->id) . '" class="delete-role btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('dashboard.roles.index');
        } catch (Exception $th) {
            createServerError($th, "indexRole", "roles");
            return false;
        }
    }

    function show($id)
    {
        try {
            $role = Role::find($id);
            return response()->json(['success' => true, 'data' => $role]);
        } catch (Exception $th) {
            createServerError($th, "showRole", "roles");
            return false;
        }
    }

    function store(CreateRoleRequest $request)
    {
        try {
            $data = $request->validated();
            $data['name'] =  Str::slug($data['name']);
            Role::create($data);
            return response()->json(['success' => true, 'message' => 'Role has been created successfully']);
        } catch (Exception $th) {
            createServerError($th, "indexRole", "roles");
            return false;
        }
    }

    function update(UpdateRoleRequest $request)
    {
        try {
            $data = $request->validated();
            Role::find($data['role_id'])->update([
                'name' => Str::slug($data['name'])
            ]);
            return response()->json(['success' => true, 'message' => 'Role has been updated successfully']);
        } catch (Exception $th) {
            createServerError($th, "indexRole", "roles");
            return false;
        }
    }

    function delete($id)
    {
        try {
            Role::find($id)->delete();
            return response()->json(['success' => true, 'message' => 'Role has been deleted successfully']);
        } catch (Exception $th) {
            createServerError($th, "indexRole", "roles");
            return false;
        }
    }
}
