<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\clients\CreateClientRequest;
use App\Http\Requests\clients\UpdateClientRequest;
use App\Models\Client;
use App\Models\ClientRole;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ClientController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $clients = Client::get();
                return DataTables::of($clients)
                    ->addColumn('actions', function ($client) {
                        $actions = '';
                        Auth::user()->hasPermissionTo('update_client') ? $actions .= '<a data-id="' . $client->id . '" href="' . route('clients.edit', $client->id) . '" class="update-client btn btn-icon btn-outline-primary m-1"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>' : '';
                        Auth::user()->hasPermissionTo('delete_client') ? $actions .= '<a href="#" data-url="' . route('clients.delete', $client->id) . '" class="delete-client btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>' : '';
                        return $actions;
                    })
                    ->editColumn('contact_button_link', function ($row) {
                        return '<a target="_blank" class="btn btn-label-linkedin" href="' . $row->contact_button_link . '"> Link </a>';
                    })
                    ->editColumn('role', function ($row) {
                        return $row->roleModel?->name;
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
        } catch (Exception $th) {
            createServerError($th, "indexClient", "clients");
            return false;
        }
    }

    function create()
    {
        try {
            $roles = ClientRole::whereNotIn('name', ['super-admin'])->pluck('name', 'id');
            return view('dashboard.client.create', ['roles' => $roles]);
        } catch (Exception $th) {
            createServerError($th, "createClient", "clients");
            return false;
        }
    }

    function edit($id)
    {
        try {
            $client = Client::find($id);
            $roles = ClientRole::whereNotIn('name', ['super-admin'])->pluck('name', 'id');
            return view('dashboard.client.update', ['roles' => $roles, 'client' => $client]);
        } catch (Exception $th) {
            createServerError($th, "editClient", "clients");
            return false;
        }
    }

    function store(CreateClientRequest $request)
    {
        try {
            $data = $request->validated();
            $data['logo'] = $request->hasFile('logo') ? 'storage/' . uploadFile($request->file('logo'), 'clients/client_logo') : null;
            $data['profile_picture'] = $request->hasFile('profile_picture') ? 'storage/' . uploadFile($request->file('profile_picture'), 'clients/client_profile_picture') : null;
            Client::create($data);
            session()->flash('success', 'Client has been created successfully');
            return response()->json(['success' => true, 'url' => route('clients.index')]);
        } catch (Exception $th) {
            createServerError($th, "storeClient", "clients");
            return false;
        }
    }

    function update(UpdateClientRequest $request)
    {
        try {
            $data = $request->validated();
            $client = Client::find($data['client_id']);
            $oldClient = clone $client;
            $data['logo'] = $request->hasFile('logo') ? 'storage/' . uploadFile($request->file('logo'), 'clients/client_logo') : $client->logo;
            $data['profile_picture'] = $request->hasFile('profile_picture') ? 'storage/' . uploadFile($request->file('profile_picture'), 'clients/client_profile_picture') : $client->profile_picture;
            unset($data['client_id']);
            $client->update($data);
            // remove old images
            if($request->hasFile('logo')){
                $logo = str_replace("storage/", "", $oldClient->logo);
                deleteFile($logo);
            }
            if($request->hasFile('profile_picture')){
                $profilePicture = str_replace("storage/", "", $oldClient->profile_picture);
                deleteFile($profilePicture);
            }
            session()->flash('success', 'Client has been updated successfully');
            return response()->json(['success' => true, 'url' => route('clients.index')]);
        } catch (Exception $th) {
            createServerError($th, "updateClient", "clients");
            return false;
        }
    }

    function delete($id)
    {
        try {
            $client = Client::find($id);
            $logo = str_replace("storage/", "", $client->logo);
            $profile_picture = str_replace("storage/", "", $client->profile_picture);
            deleteFile($logo);
            deleteFile($profile_picture);
            $client->delete();
            $count = Client::count();
            session()->flash('success', 'Client has been deleted successfully');
            return response()->json(['success' => true, 'count' => $count]);
        } catch (Exception $th) {
            createServerError($th, "deleteClient", "clients");
            return false;
        }
    }
}
