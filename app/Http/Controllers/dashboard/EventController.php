<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\events\CreateEventRequest;
use App\Http\Requests\events\folders\CreateFolderRequest;
use App\Http\Requests\events\UpdateEventRequest;
use App\Models\Client;
use App\Models\ClientRole;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Vendor;
use App\Services\BunnyImageService;
use App\Services\BunnyVideoService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public BunnyImageService $bunnyImageService;
    protected BunnyVideoService $bunnyVideoService;
    public function __construct(BunnyImageService $bunnyImageService, BunnyVideoService $bunnyVideoService)
    {
        $this->bunnyImageService = $bunnyImageService;
        $this->bunnyVideoService = $bunnyVideoService;
    }

    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $filterStatus = $request->filter_status;
                $filterClient = $request->filter_client;
                $filterType = $request->filter_type;
                $filterDate = $request->filter_date;

                $eventsQuery = Event::with(['client', 'type'])
                    ->when(!empty($filterClient), fn($query) => $query->where('client_id', $filterClient))
                    ->when(!empty($filterType), fn($query) => $query->where('event_type_id', $filterType))
                    ->when(!empty($filterDate), fn($query) => $query->whereDate('start_date', $filterDate))
                    ->orderBy('created_at', 'desc');
                $events = $eventsQuery->get()->filter(function ($item) use ($filterStatus) {
                    if (!$filterStatus) return true;
                    $status = eventStatus($item);
                    return $status === $filterStatus;
                });

                return DataTables::of($events)
                    ->addColumn('event_status', function ($row) {
                        return eventStatus($row);
                    })
                    ->addColumn('event_type', fn($row) => $row->type?->name)
                    ->addColumn('event_client', fn($row) => $row->client?->planner_name)
                    ->addColumn('time_reminder', function ($row) {
                        $eventEndDate = Carbon::parse($row->end_date);
                        $now = Carbon::now();
                        return $eventEndDate->gt($now) ? $eventEndDate->diffInDays($now) . ' days' : '-';
                    })
                    ->editColumn('qr_code', fn($row) => '<a href="/' . $row->qr_code . '" download><img src="/' . $row->qr_code . '" width="100" height="100"></a>')
                    ->editColumn('event_name', fn($row) => Auth::user()->hasAnyPermission(['create_folder', 'update_folder', 'delete_folder', 'list_folders']) ? '<a href="' . route('folders.index', $row->bunny_event_name) . '">' . $row->event_name . '</a>' : $row->event_name)
                    ->editColumn('profile_picture', fn($row) => $row->profile_picture ? '<img src="/' . $row->profile_picture . '" width="100" height="100">' : '')
                    ->editColumn('cover_image', fn($row) => $row->cover_image ? '<img src="/' . $row->cover_image . '" width="100" height="100">' : '')
                    ->editColumn('event_link', function ($row) {
                        $status = eventStatus($row);
                        if ($status == 'Expired')
                            return '<a target="_blank" class="btn btn-label-linkedin" href="' . route('events.expired', ['event_slug' => $row->bunny_event_name, 'year' => $row->bunny_main_folder_name]) . '"> Link </a>';
                        if ($status == 'Pending')
                            return '<a target="_blank" class="btn btn-label-linkedin" href="' . route('events.pending', ['event_slug' => $row->bunny_event_name, 'year' => $row->bunny_main_folder_name]) . '"> Link </a>';
                        return '<a target="_blank" class="btn btn-label-linkedin" href="' . $row->event_link . '"> Link </a>';
                    })
                    ->addColumn('actions', function ($event) {
                        $actions = '';
                        $user = Auth::user();
                        if ($user->hasPermissionTo('update_event')) {
                            $actions .= '<a href="' . route('events.edit', $event->id) . '" class="update-event btn btn-icon btn-outline-primary m-1"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>';
                        }
                        if ($user->hasPermissionTo('delete_event')) {
                            $actions .= '<a href="#" data-url="' . route('events.delete', $event->id) . '" class="delete-event btn btn-icon btn-outline-primary m-1"><i class="bx bx-trash" style="color:red"></i></a>';
                        }
                        if ($user->hasAnyPermission(['create_folder', 'update_folder', 'delete_folder', 'list_folders'])) {
                            $actions .= '<a title="Folders" href="' . route('folders.index', $event->bunny_event_name) . '" class="btn rounded-pill btn-icon btn-primary"><i class="bx bx-folder" style="color:white"></i></a>';
                        }
                        return $actions;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['qr_code', 'event_name', 'profile_picture', 'event_link', 'cover_image', 'actions'])
                    ->make(true);
            }

            $clients = Client::whereHas('event')->pluck('planner_name', 'id')->toArray();
            $types = EventType::whereHas('event')->pluck('name', 'id')->toArray();
            $statuses = ['Expired', 'Pending', 'Online', 'Expire soon'];
            return view('dashboard.event.index', ['clients' => $clients, 'types' => $types, 'statuses' => $statuses]);
        } catch (Exception $th) {
            createServerError($th, "indexEvent", "events");
            return false;
        }
    }

    function create()
    {
        try {
            $types = EventType::pluck('name', 'id');
            $vendors = Vendor::pluck('vendor_name', 'id');
            $clients = Client::pluck('planner_name', 'id');
            return view('dashboard.event.create', ['types' => $types, 'vendors' => $vendors, 'clients' => $clients]);
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
                'bunny_main_folder_name' => Carbon::parse($data['start_date'])->year,
                'enable_organizer' => isset($data['enable_organizer']) ? 1 : 0
            ]);
            $allowGuestUpload = isset($data['image_share_guest_book']) && $data['image_share_guest_book'] == 'on' ? 1 : 0;
            $event->setting()->create([
                'image_share_guest_book' => $allowGuestUpload,
                'image_folders' => isset($data['image_folders']) && $data['image_folders'] == 'on' ? 1 : 0,
                'video_playlist' => isset($data['video_playlist']) && $data['video_playlist'] == 'on' ? 1 : 0,
                'allow_upload' => isset($data['allow_upload']) && $data['allow_upload'] == 'on' ? 1 : 0,
                'auto_image_approve' => isset($data['auto_image_approve']) && $data['auto_image_approve'] == 'on' ? 1 : 0,
                'allow_image_download' => isset($data['allow_image_download']) && $data['allow_image_download'] == 'on' ? 1 : 0,
                'theme' => isset($data['theme']) ? $data['theme'] : 'light',
                'accent_color' => isset($data['accent_color']) ? $data['accent_color'] : '',
                'font' => isset($data['font']) ? $data['font'] : ''
            ]);
            $event->organizers()->createMany($data['organizers']);
            if ($allowGuestUpload)
                app(FolderController::class)->store(new CreateFolderRequest(['folder_type' => 'image', 'folder_name' => 'Guest Upload', 'folder_thumbnail' => 'assets/img/folders/upload-folder-default.jpg']), $event->bunny_event_name);
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
            $vendors = Vendor::pluck('vendor_name', 'id');
            return view('dashboard.event.update', ['event' => $event, 'types' => $types, 'roles' => $roles, 'clients' => $clients, 'vendors' => $vendors]);
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
            $data['cover_image'] = $request->hasFile('cover_image') ? 'storage/' . uploadFile($request->file('cover_image'), 'events/event_cover_image') :  $event->cover_image;
            $data['profile_picture'] = $request->hasFile('profile_picture') ? 'storage/' . uploadFile($request->file('profile_picture'), 'events/profile_picture') :  $event->profile_picture;
            $data['qr_code'] = $oldEvent->event_link != $data['event_link'] ? 'storage/' . uploadBase64File($data['qr_code'], 'events/event_qr_code') : $event->qr_code;
            $setting =  $event->setting;
            $allowGuestUpload = isset($data['image_share_guest_book']) && $data['image_share_guest_book'] == 'on' ? 1 : 0;
            $setting->update([
                'image_share_guest_book' => $allowGuestUpload,
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
                'qr_code' => $data['qr_code'],
                'enable_organizer' => isset($data['enable_organizer']) ? 1 : 0
            ];
            if ($event && $event->canUpdateEventNameAndStartDate()) {
                $eventData['event_name'] = $data['event_name'];
                $eventData['start_date'] = $data['start_date'];
            }

            $event->update($eventData);
            if ($allowGuestUpload && $event->folders->where('folder_name', 'Guest Upload')->count() == 0)
                app(FolderController::class)->store(new CreateFolderRequest(['folder_type' => 'image', 'folder_name' => 'Guest Upload', 'folder_thumbnail' => 'assets/img/folders/upload-folder-default.jpg']), $event->bunny_event_name);
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
            return response()->json(['success' => true, 'url' => route('events.edit', $id)]);
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
            $event->folders->map(function ($folder) use ($event) {
                app(FolderController::class)->delete($event->bunny_event_name, $folder->id);
            });
            $this->bunnyImageService->deleteFolderItSelf($event->bunny_main_folder_name . '/' . $event->bunny_event_name . '/');
            $event->delete();
            $count = Event::count();
            $this->bunnyVideoService->deleteCollection($event->video_collection_id);
            return response()->json(['success' => true, 'count' => $count]);
        } catch (Exception $th) {
            createServerError($th, "deleteEvent", "events");
            return false;
        }
    }

    function expired(Request $request)
    {
        $year = $request->route('year');
        $eventSlug = $request->route('event_slug');
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        $status = eventStatus($event);
        if ($status == 'Expired')
            return view('website.pages.event_expired', ['event' => $event, 'year' => $year, 'event_slug' => $eventSlug]);
        if ($status == 'Pending')
            return view('website.pages.event_pending', ['event' => $event, 'year' => $year, 'event_slug' => $eventSlug]);
        return view('website.pages.index', ['year' => $year, 'event_slug' => $eventSlug, 'event' => $event]);
    }

    function pending(Request $request)
    {
        $year = $request->route('year');
        $eventSlug = $request->route('event_slug');
        $event = Event::where('bunny_event_name', $eventSlug)->first();

        $status = eventStatus($event);
        if ($status == 'Expired')
            return view('website.pages.event_expired', ['event' => $event, 'year' => $year, 'event_slug' => $eventSlug]);
        if ($status == 'Pending')
            return view('website.pages.event_pending', ['event' => $event, 'year' => $year, 'event_slug' => $eventSlug]);
        return view('website.pages.index', ['year' => $year, 'event_slug' => $eventSlug, 'event' => $event]);
    }
}
