<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\clients\CreateClientRequest;
use App\Http\Requests\clients\UpdateClientRequest;
use App\Models\Client;
use App\Models\ClientRole;
use App\Models\User;
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
                $clients = Client::orderBy('created_at', 'desc')->get();
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
                    ->editColumn('logo', function ($row) {
                        return $row->logo ? '<img src="/' . $row->logo . '" alt="" width="100px" height="100px">' : null;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['contact_button_link', 'logo', 'actions'])
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
            unset($data['password_confirmation']);
            $data['logo'] = $request->hasFile('logo') ? 'storage/' . uploadFile($request->file('logo'), 'clients/client_logo') : null;
            $client = Client::create($data);
            $user = $this->createClientUser($client);
            $client->update(['user_id' => $user->id]);
            session()->flash('success', 'Client has been created successfully');
            return response()->json(['success' => true, 'url' => route('clients.index')]);
        } catch (Exception $th) {
            createServerError($th, "storeClient", "clients");
            return false;
        }
    }

    function createClientUser($client)
    {
        $data['name'] = $client->planner_name;
        $data['full_name'] = $client->planner_name;
        $data['phone'] = $client->phone_number;
        $data['email'] = $client->email;
        $data['password'] = $client->password;
        $data['user_type'] = 'client';
        $data['dashboard_user'] = 1;
        $user = User::create($data);
        $this->clientUserAssignPermissions($user);
        return $user;
    }

    function clientUserAssignPermissions($user)
    {
        $permissions = [
            'update_event',
            'list_events',
            'list_folders',
            'update_folder',
            'upload_image',
            'delete_image',
            'update_image',
            'approve_decline_image'
        ];
        $user->givePermissionTo($permissions);
    }

    function update(UpdateClientRequest $request)
    {
        try {
            $data = $request->validated();
            unset($data['password_confirmation']);
            $client = Client::find($data['client_id']);
            $oldClient = clone $client;
            $data['logo'] = $request->hasFile('logo') ? 'storage/' . uploadFile($request->file('logo'), 'clients/client_logo') : $client->logo;
            unset($data['client_id']);
            $client->update($data);
            $this->updateClientUser($client);
            // remove old images
            if ($request->hasFile('logo')) {
                $logo = str_replace("storage/", "", $oldClient->logo);
                deleteFile($logo);
            }
            session()->flash('success', 'Client has been updated successfully');
            return response()->json(['success' => true, 'url' => route('clients.edit', $client->id)]);
        } catch (Exception $th) {
            createServerError($th, "updateClient", "clients");
            return false;
        }
    }


    function updateClientUser($client)
    {
        $data['name'] = $client->planner_name;
        $data['full_name'] = $client->planner_name;
        $data['phone'] = $client->phone_number;
        $data['email'] = $client->email;
        $data['password'] = $client->password;
        User::find($client->user_id)->update($data);
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
