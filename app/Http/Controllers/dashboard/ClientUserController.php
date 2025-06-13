<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\clients_users\CreateClientUserRequest;
use App\Http\Requests\clients_users\UpdateClientUserRequest;
use App\Models\Client;
use App\Models\User;
use App\Models\UserClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class ClientUserController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $clients = UserClient::orderBy('created_at', 'desc')->get();
                return DataTables::of($clients)
                    ->addColumn('name', function ($row) {
                        return $row->client->planner_name;
                    })
                    ->addColumn('planner_business_name', function ($row) {
                        return $row->client->planner_business_name;
                    })
                    ->addColumn('phone_number', function ($row) {
                        return $row->client->phone_number;
                    })
                    ->addColumn('email', function ($row) {
                        return $row->client->email;
                    })
                    ->addColumn('client_role', function ($row) {
                        return $row->client->client_role;
                    })
                    ->addColumn('logo', function ($row) {
                        return $row->client->logo;
                    })
                    ->addColumn('actions', function ($client) {
                        $actions = '';
                        Auth::user()->hasPermissionTo('update_client_user') ? $actions .= '<a data-id="' . $client->id . '" href="' . route('clients.users.edit', $client->id) . '" class="update-client btn btn-icon btn-outline-primary m-1"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>' : '';
                        Auth::user()->hasPermissionTo('delete_client_user') ? $actions .= '<a href="#" data-url="' . route('clients.users.delete', $client->id) . '" class="delete-client btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>' : '';
                        return $actions;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('dashboard.client_user.index');
        } catch (Exception $th) {
            createServerError($th, "indexClient", "clients");
            return false;
        }
    }

    function create()
    {
        try {
            $currentClientUSers = UserClient::pluck("client_id")->toArray();
            $clients = Client::whereNotIn('id', $currentClientUSers)->pluck('planner_name', 'id');
            return view('dashboard.client_user.create', ['clients' => $clients]);
        } catch (Exception $th) {
            createServerError($th, "createClient", "clients");
            return false;
        }
    }

    function edit($id)
    {
        try {
            $clientUser = UserClient::find($id);
            $currentClientUSers = UserClient::where("client_id", "!=",  $clientUser->client->id)->pluck("client_id")->toArray();
            $clients = Client::whereNotIn('id', $currentClientUSers)->pluck('planner_name', 'id');
            return view('dashboard.client_user.update', ['clients' => $clients, 'clientUser' => $clientUser]);
        } catch (Exception $th) {
            createServerError($th, "editClient", "clients");
            return false;
        }
    }

    function store(CreateClientUserRequest $request)
    {
        try {
            $data = $request->validated();
            unset($data['password_confirmation']);
            $client = Client::find($data['client_id']);
            $user = $this->createNewUser($client, $data);
            $data['user_id'] = $user->id;
            unset($data['password']);
            UserClient::create($data);
            session()->flash('success', 'Client has been created successfully');
            return response()->json(['success' => true, 'url' => route('clients.users.index')]);
        } catch (Exception $th) {
            createServerError($th, "storeClient", "clients");
            return false;
        }
    }

    function createNewUser($client, $newData)
    {
        $data['name'] = $newData['name'];
        $data['full_name'] = $newData['name'];
        $data['phone'] = $client->phone_number;
        $data['email'] = $client->email;
        $data['password'] = $newData['password'];
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

    function update(UpdateClientUserRequest $request)
    {
        try {
            $data = $request->validated();
            $clientUser = UserClient::find($data['client_user_id']);
            unset($data['password_confirmation'], $data['client_user_id']);
            $this->updateClientUser($clientUser, $data);
            session()->flash('success', 'Client has been updated successfully');
            return response()->json(['success' => true, 'url' => route('clients.users.edit', $clientUser->id)]);
        } catch (Exception $th) {
            dd('' . $th->getMessage());
            createServerError($th, "updateClient", "clients");
            return false;
        }
    }

    function updateClientUser($clientUser, $newData)
    {
        if ($newData["client_id"] != $clientUser->client_id) {
            User::find($clientUser->user_id)?->delete();
            $user = $this->createNewUser(Client::find($newData["client_id"]), $newData);
            $clientUser->client_id = $newData["client_id"];
            $clientUser->user_id = $user->id;
        }
        $clientUser->name = $newData["name"];
        $clientUser->save();
    }

    function delete($id)
    {
        try {
            $clientUser = UserClient::find($id);
            $clientUser->user->delete();
            $clientUser->delete();
            session()->flash('success', 'Client user has been deleted successfully');
            return response()->json(['success' => true]);
        } catch (Exception $th) {
            createServerError($th, "deleteClient", "clients");
            return false;
        }
    }
}
