<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\roles\CreateRoleRequest;
use App\Http\Requests\roles\UpdateRoleRequest;
use App\Models\ClientRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $events = ClientRole::whereNotIn('name', ['super-admin'])->get();
                return DataTables::of($events)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($role) {
                        $actions = '';
                        Auth::user()->hasPermissionTo('update_role') ? $actions .= '<a href="#" data-id=' . $role->id . ' class="update-role btn btn-icon btn-outline-primary m-1"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>' : '';
                        Auth::user()->hasPermissionTo('delete_role') ? $actions .= '<a href="#" data-url="' . route('roles.delete', $role->id) . '" class="delete-role btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>' : '';
                        return $actions;
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
            $role = ClientRole::find($id);
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
            ClientRole::create($data);
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
            ClientRole::find($data['role_id'])->update([
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
            ClientRole::find($id)->delete();
            return response()->json(['success' => true, 'message' => 'Role has been deleted successfully']);
        } catch (Exception $th) {
            createServerError($th, "indexRole", "roles");
            return false;
        }
    }
}
