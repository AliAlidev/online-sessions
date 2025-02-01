<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\clients\CreateClientRequest;
use App\Http\Requests\clients\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class ClientController extends Controller
{
    function index(Request $request)
    {
        if ($request->ajax()) {
            $clients = Client::get();
            return DataTables::of($clients)
                ->addColumn('actions', function ($client) {
                    return '<a data-id="' . $client->id . '" href="' . route('clients.edit', $client->id) . '" class="update-client btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt"></i></a>
                            <a href="#" data-url="' . route('clients.delete', $client->id) . '" class="delete-client btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>';
                })
                ->editColumn('contact_button_link', function ($row) {
                    return '<a target="_blank" class="btn btn-label-linkedin" href="' . $row->contact_button_link . '"> Link </a>';
                })
                ->editColumn('role', function ($row) {
                    return $row->roleModel->name;
                })
                ->editColumn('logo', function ($row) {
                    return $row->logo ? '<img src="/' . $row->logo . '" alt="" width="100px" height="100px">' : null;
                })
                ->editColumn('profile_picture', function ($row) {
                    return $row->profile_picture ? '<img src="/' . $row->profile_picture . '" alt="" width="100px" height="100px">' : null;
                })
                ->addIndexColumn()
                ->rawColumns(['contact_button_link', 'logo', 'profile_picture', 'actions'])
                ->make(true);
        }
        return view('dashboard.client.index');
    }

    function create()
    {
        $roles = Role::pluck('name', 'id');
        return view('dashboard.client.create', ['roles' => $roles]);
    }

    function edit($id)
    {
        $client = Client::find($id);
        $roles = Role::pluck('name', 'id');
        return view('dashboard.client.update', ['roles' => $roles, 'client' => $client]);
    }

    function store(CreateClientRequest $request)
    {
        $data = $request->validated();
        $data['logo'] = $request->hasFile('logo') ? 'storage/' . uploadFile($request->file('logo'), 'client_logo') : null;
        $data['profile_picture'] = $request->hasFile('profile_picture') ? 'storage/' . uploadFile($request->file('profile_picture'), 'client_profile_picture') : null;
        Client::create($data);
        session()->flash('success', 'Client has been created successfully');
        return response()->json(['success' => true, 'url' => route('clients.index')]);
    }

    function update(UpdateClientRequest $request)
    {
        $data = $request->validated();
        $client = Client::find($data['client_id']);
        $data['logo'] = $request->hasFile('logo') ? 'storage/' . uploadFile($request->file('logo'), 'client_logo') : $client->logo;
        $data['profile_picture'] = $request->hasFile('profile_picture') ? 'storage/' . uploadFile($request->file('profile_picture'), 'client_profile_picture') : $client->profile_picture;
        unset($data['client_id']);
        $client->update($data);
        session()->flash('success', 'Client has been updated successfully');
        return response()->json(['success' => true, 'url' => route('clients.index')]);
    }

    function delete($id) {
        $client = Client::find($id);
        $logo = str_replace("storage/", "", $client->logo);
        $profile_picture = str_replace("storage/", "", $client->profile_picture);
        deleteFile($logo);
        deleteFile($profile_picture);
        $client->delete();
        $count = Client::count();
        session()->flash('success', 'Client has been deleted successfully');
        return response()->json(['success' => true, 'count' => $count]);
    }
}
