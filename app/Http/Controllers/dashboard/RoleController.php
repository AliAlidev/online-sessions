<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\roles\CreateRoleRequest;
use App\Http\Requests\roles\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    function index(Request $request)
    {
        if ($request->ajax()) {
            $events = Role::get();
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
    }

    function show($id)
    {
        $role = Role::find($id);
        return response()->json(['success' => true, 'data' => $role]);
    }

    function store(CreateRoleRequest $request)
    {
        $data = $request->validated();
        $data['name'] =  Str::slug($data['name']);
        Role::create($data);
        return response()->json(['success' => true, 'message' => 'Role has been created successfully']);
    }

    function update(UpdateRoleRequest $request)
    {
        $data = $request->validated();
        Role::find($data['role_id'])->update([
            'name' => Str::slug($data['name'])
        ]);
        return response()->json(['success' => true, 'message' => 'Role has been updated successfully']);
    }

    function delete($id)
    {
        Role::find($id)->delete();
        return response()->json(['success' => true, 'message' => 'Role has been deleted successfully']);
    }
}
