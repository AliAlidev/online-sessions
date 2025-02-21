<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\event_types\UpdateEventTypeRequest;
use App\Http\Requests\events\CreateEventRequest;
use App\Http\Requests\events\UpdateEventRequest;
use App\Models\Client;
use App\Models\ClientRole;
use App\Models\Event;
use App\Models\EventSetting;
use App\Models\EventType;
use App\Services\BunnyImageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public BunnyImageService $bunnyImageService;
    public function __construct(BunnyImageService $bunnyImageService)
    {
        $this->bunnyImageService = $bunnyImageService;
    }

    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $events = Event::orderBy('created_at', 'desc')->get();
                return DataTables::of($events)
                    ->editColumn('qr_code', function ($row) {
                        return '<a href="/' . $row->qr_code . '" download><img src="/' . $row->qr_code . '" alt="" width="100px" height="100px"></a>';
                    })
                    ->editColumn('profile_picture', function ($row) {
                        return $row->profile_picture ? '<img src="/' . $row->profile_picture . '" alt="" width="100px" height="100px">' : '';
                    })
                    ->editColumn('cover_image', function ($row) {
                        return $row->cover_image ? '<img src="/' . $row->cover_image . '" alt="" width="100px" height="100px">' : '';
                    })
                    ->addColumn('event_type', function ($row) {
                        return $row->type?->name;
                    })
                    ->addColumn('event_client', function ($row) {
                        return $row->client?->planner_name;
                    })
                    ->editColumn('event_link', function ($row) {
                        return '<a target="_blank" class="btn btn-label-linkedin" href="' . $row->event_link . '"> Link </a>';
                    })
                    ->addColumn('actions', function ($event) {
                        $actions = '';
                        Auth::user()->hasPermissionTo('update_event') ? $actions .= '<a href="' . route('events.edit', $event->id) . '" class="update-event btn btn-icon btn-outline-primary m-1"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>' : '';
                        Auth::user()->hasPermissionTo('delete_event') ? $actions .= '<a href="#" data-url="' . route('events.delete', $event->id) . '" class="delete-event btn btn-icon btn-outline-primary m-1"><i class="bx bx-trash" style="color:red"></i> </a>' : '';
                        Auth::user()->hasAnyPermission(['create_folder', 'update_folder', 'delete_folder']) ? $actions .= '<a title="Folders" href="' . route('folders.index', $event->id) . '" class="btn rounded-pill btn-icon btn-primary"><i class="bx bx-folder" style="color:white"></i> </a>' : '';
                        return $actions;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['qr_code', 'profile_picture', 'event_link', 'cover_image', 'actions'])
                    ->make(true);
            }
            return view('dashboard.event.index');
        } catch (Exception $th) {
            createServerError($th, "indexEvent", "events");
            return false;
        }
    }

    function create()
    {
        try {
            $types = EventType::pluck('name', 'id');
            $roles = ClientRole::whereNotIn('name', ['super-admin'])->pluck('name', 'id');
            $clients = Client::pluck('planner_name', 'id');
            return view('dashboard.event.create', ['types' => $types, 'roles' => $roles, 'clients' => $clients]);
        } catch (Exception $th) {
            createServerError($th, "createEvent", "events");
            return false;
        }
    }

    function store(CreateEventRequest $request)
    {
        try {
            $data = $request->validated();
            $data['cover_image'] = $request->hasFile('cover_image') ? 'storage/' . uploadFile($request->file('cover_image'), 'events/event_cover_image') : null;
            $data['profile_picture'] = $request->hasFile('profile_picture') ? 'storage/' . uploadFile($request->file('profile_picture'), 'events/profile_picture') : null;
            $data['qr_code'] = 'storage/' . uploadBase64File($data['qr_code'], 'events/event_qr_code');
            $event = Event::create([
                'event_name' => $data['event_name'],
                'event_alias_name' => $data['event_alias_name'],
                'bunny_event_name' => Str::slug($data['event_name']),
                'cover_image' => $data['cover_image'],
                'event_type_id' => $data['event_type_id'],
                'profile_picture' => $data['profile_picture'],
                'client_id' => $data['client_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'customer' => $data['customer'],
                'venue' => $data['venue'],
                'active_duration' => $data['active_duration'],
                'description' => $data['description'],
                'event_link' => $data['event_link'],
                'event_password' => $data['event_password'],
                'welcome_message' => $data['welcome_message'],
                'qr_code' => $data['qr_code'],
                'bunny_main_folder_name' => Carbon::parse($data['start_date'])->year
            ]);
            $event->setting()->create([
                'image_share_guest_book' => isset($data['image_share_guest_book']) && $data['image_share_guest_book'] == 'on' ? 1 : 0,
                'image_folders' => isset($data['image_folders']) && $data['image_folders'] == 'on' ? 1 : 0,
                'video_playlist' => isset($data['video_playlist']) && $data['video_playlist'] == 'on' ? 1 : 0,
                'allow_upload' => isset($data['allow_upload']) && $data['allow_upload'] == 'on' ? 1 : 0,
                'auto_image_approve' => isset($data['auto_image_approve']) && $data['auto_image_approve'] == 'on' ? 1 : 0,
                'allow_image_download' => isset($data['allow_image_download']) && $data['allow_image_download'] == 'on' ? 1 : 0,
                'theme' => isset($data['theme']) ? $data['theme'] : '',
                'accent_color' => isset($data['accent_color']) ? $data['accent_color'] : '',
                'font' => isset($data['font']) ? $data['font'] : ''
            ]);
            $event->organizers()->createMany($data['organizers']);
            session()->flash('success', 'Event has been created successfully');
            return response()->json(['success' => true, 'url' => route('events.index')]);
        } catch (Exception $th) {
            createServerError($th, "storeEvent", "events");
            return false;
        }
    }

    function edit($id)
    {
        try {
            $event = Event::with(['type', 'setting', 'organizers'])->find($id);
            $types = EventType::pluck('name', 'id');
            $roles = ClientRole::whereNotIn('name', ['super-admin'])->pluck('name', 'id');
            $clients = Client::pluck('planner_name', 'id');
            return view('dashboard.event.update', ['event' => $event, 'types' => $types, 'roles' => $roles, 'clients' => $clients]);
        } catch (Exception $th) {
            createServerError($th, "editEvent", "events");
            return false;
        }
    }

    function update(UpdateEventRequest $request, $id)
    {
        try {
            $event = Event::find($id);
            $oldEvent = clone $event;
            $data = $request->validated();
            $data['cover_image'] = $request->hasFile('cover_image') ? 'storage/' . uploadFile($request->file('cover_image'), 'events/event_cover_image') :  $event->event_cover_image;
            $data['profile_picture'] = $request->hasFile('profile_picture') ? 'storage/' . uploadFile($request->file('profile_picture'), 'events/profile_picture') :  $event->profile_picture;
            $data['qr_code'] = $oldEvent->event_link != $data['event_link'] ? 'storage/' . uploadBase64File($data['qr_code'], 'events/event_qr_code') : $event->qr_code;
            $event->setting()->update([
                'image_share_guest_book' => isset($data['image_share_guest_book']) && $data['image_share_guest_book'] == 'on' ? 1 : 0,
                'image_folders' => isset($data['image_folders']) && $data['image_folders'] == 'on' ? 1 : 0,
                'video_playlist' => isset($data['video_playlist']) && $data['video_playlist'] == 'on' ? 1 : 0,
                'allow_upload' => isset($data['allow_upload']) && $data['allow_upload'] == 'on' ? 1 : 0,
                'auto_image_approve' => isset($data['auto_image_approve']) && $data['auto_image_approve'] == 'on' ? 1 : 0,
                'allow_image_download' => isset($data['allow_image_download']) && $data['allow_image_download'] == 'on' ? 1 : 0,
                'theme' => isset($data['theme']) ? $data['theme'] : '',
                'accent_color' => isset($data['accent_color']) ? $data['accent_color'] : '',
                'font' => isset($data['font']) ? $data['font'] : ''
            ]);
            $eventData = [
                'event_alias_name' => $data['event_alias_name'],
                'cover_image' => $data['cover_image'],
                'event_type_id' => $data['event_type_id'],
                'profile_picture' => $data['profile_picture'],
                'client_id' => $data['client_id'],
                'end_date' => $data['end_date'],
                'customer' => $data['customer'],
                'venue' => $data['venue'],
                'active_duration' => $data['active_duration'],
                'description' => $data['description'],
                'event_link' => $data['event_link'],
                'event_password' => $data['event_password'],
                'welcome_message' => $data['welcome_message'],
                'qr_code' => $data['qr_code']
            ];
            if ($event && $event->canUpdateEventNameAndStartDate()) {
                $eventData['event_name'] = $data['event_name'];
                $eventData['start_date'] = $data['start_date'];
            }

            $event->update($eventData);
            $event->organizers()->whereNotIn('id', array_column($data['organizers'], 'organizer_model_id'))->delete();
            array_map(function ($organizer) use ($event) {
                if (isset($organizer['organizer_model_id'])) {
                    $rowId = $organizer['organizer_model_id'];
                    unset($organizer['organizer_model_id']);
                    $event->organizers()->find($rowId)->update($organizer);
                } else {
                    unset($organizer['organizer_model_id']);
                    $event->organizers()->create($organizer);
                }
            }, $data['organizers']);
            if ($request->hasFile('cover_image')) {
                $coverImage = str_replace("storage/", "", $oldEvent->cover_image);
                deleteFile($coverImage);
            }
            if ($request->hasFile('profile_picture')) {
                $profilePicture = str_replace("storage/", "", $oldEvent->profile_picture);
                deleteFile($profilePicture);
            }
            if ($oldEvent->event_link != $data['event_link']) {
                $qrCode = str_replace("storage/", "", $oldEvent->qr_code);
                deleteFile($qrCode);
            }

            session()->flash('success', 'Event has been updated successfully');
            return response()->json(['success' => true, 'url' => route('events.index')]);
        } catch (Exception $th) {
            createServerError($th, "updateEvent", "events");
            return false;
        }
    }

    function delete($id)
    {
        try {
            $event = Event::find($id);
            $coverImage = str_replace("storage/", "", $event->cover_image);
            deleteFile($coverImage);
            $profilePicture = str_replace("storage/", "", $event->profile_picture);
            deleteFile($profilePicture);
            $qrCode = str_replace("storage/", "", $event->qr_code);
            deleteFile($qrCode);
            $event->folders->map(function ($folder) {
                app(FolderController::class)->delete($folder->id);
            });
            $this->bunnyImageService->deleteFolderItSelf($event->bunny_main_folder_name . '/' . $event->event_name . '/');
            $event->delete();
            $count = Event::count();
            return response()->json(['success' => true, 'count' => $count]);
        } catch (Exception $th) {
            createServerError($th, "deleteEvent", "events");
            return false;
        }
    }
}
