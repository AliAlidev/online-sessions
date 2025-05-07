<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventFolder;
use App\Models\FolderFile;
use App\Services\BunnyImageService;
use App\Services\BunnyVideoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class WebsiteController extends Controller
{
    protected BunnyImageService $bunnyService;
    protected BunnyVideoService $bunnyVideoService;
    function __construct(BunnyImageService $bunnyService, BunnyVideoService $bunnyVideoService)
    {
        $this->bunnyService = $bunnyService;
        $this->bunnyVideoService = $bunnyVideoService;
    }

    function index(Request $request)
    {
        $year = $request->route('year');
        $eventSlug = $request->route('event_slug');
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        $eventStartDate = Carbon::parse($event->start_date)->startOfDay();
        $now = Carbon::now()->endOfDay();
        if (Carbon::parse($event->end_date)->isPast())
            return view('website.pages.event_expired');
        if ($eventStartDate->gt($now))
            return view('website.pages.event_pending');
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.index', ['year' => $year, 'event_slug' => $eventSlug, 'event' => $event]);
    }

    function galleryRedirectUrl(Request $request)
    {
        $event = Event::where('bunny_event_name', $request->event_slug)->first();
        $wantsPassword = false;
        $url = route('landing.gallery', ['year' => $request->year, 'event_slug' => $request->event_slug]);
        if ($event->event_password != null) {
            $url = route('landing.event_password', ['year' => $request->year, 'event_slug' => $request->event_slug]);
            $wantsPassword = true;
        }

        return response()->json([
            'success' => true,
            'wantsPassword' => $wantsPassword,
            'url' => $url,
            'year' => $request->year,
            'event_slug' => $request->event_slug
        ]);
    }

    function eventPassword(Request $request)
    {
        $event = Event::where('bunny_event_name', $request->event_slug)->first();
        return view('website.pages.event_password', ['year' => $request->year, 'event_slug' => $request->event_slug, 'event' => $event]);
    }

    function applyEventPassword(Request $request)
    {
        $data = $request->all();
        $eventSlug = $data['event_slug'];
        $password = $data['password'];
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        if (strcmp($password, $event->event_password) == 0)
            return response()->json([
                'success' => true,
                'url' => route('landing.gallery', ['year' => $data['year'], 'event_slug' => $data['event_slug']])
            ]);
        return response()->json(['success' => false, 'message' => 'Invalid password']);
    }

    function shareRedirectUrl(Request $request)
    {
        return response()->json([
            'success' => true,
            'url' => route('landing.share', ['year' => $request->year, 'event_slug' => $request->event_slug]),
            'year' => $request->year,
            'event_slug' => $request->event_slug
        ]);
    }

    function gallery(Request $request)
    {
        $year = $request->route('year');
        $eventSlug = $request->route('event_slug');
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        if (Carbon::parse($event->end_date)->isPast())
            return view('website.pages.event_expired');
        $eventStartDate = Carbon::parse($event->start_date)->startOfDay();
        $now = Carbon::now()->endOfDay();
        if ($eventStartDate->gt($now))
            return view('website.pages.event_pending');
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        $foldersList = [];
        $event->folders()->orderBy('order', 'asc')->get()->each(function ($folder) use (&$foldersList, $event) {
            if ($event->supportShowImageFolders()) {
                if ($folder->folder_type == 'image' && $folder->folder_name != 'Guest Upload')
                    $foldersList[] = $folder;
            }
            if ($event->supportShowGuestImageFolder()) {
                if ($folder->folder_type == 'image' && $folder->folder_name == 'Guest Upload')
                    $foldersList[] = $folder;
            }
            if ($event->supportShowVideoFolders()) {
                if ($folder->folder_type == 'video')
                    $foldersList[] = $folder;
            }
            if ($folder->folder_type == 'link' || $folder->folder_type == 'fake')
                $foldersList[] = $folder;
        });
        return view('website.pages.gallery.gallery', ['year' => $year, 'event_slug' => $eventSlug, 'event' => $event, 'folders' => $foldersList]);
    }

    function image(Request $request)
    {
        $folderId = $request->folder_id;
        $folder = EventFolder::find($folderId);
        $images = $folder->files()->where('file_status', 'approved')->orderBy('created_at','desc')->get();
        $eventSupportDownload = $folder->event->supportImageDownload();
        return response()->json([
            'success' => true,
            'html' => view('website.pages.gallery.image', ['images' => $images, 'folder' => $folder])->render(),
            'eventSupportDownload' => $eventSupportDownload
        ]);
    }

    function share(Request $request)
    {
        $year = $request->route('year');
        $eventSlug = $request->route('event_slug');
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        if (!$event->supportImageUpload())
            return redirect()->route('landing.index', ['year' => $year, 'event_slug' => $eventSlug]);
        if (Carbon::parse($event->end_date)->isPast())
            return view('website.pages.event_expired');
        $eventStartDate = Carbon::parse($event->start_date)->startOfDay();
        $now = Carbon::now()->endOfDay();
        if ($eventStartDate->gt($now))
            return view('website.pages.event_pending');
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.share', ['year' => $year, 'event_slug' => $eventSlug, 'event' => $event]);
    }

    function video(Request $request)
    {
        $folderId = $request->folder_id;
        $folder = EventFolder::find($folderId);
        $videos = $folder->files()->orderBy('file_order', 'asc')->where('file_status', 'approved')->where('bunny_status', 3)->get()->each(function ($video) {
            $video->file = str_replace('https://video.bunnycdn.com/play/', 'https://iframe.mediadelivery.net/embed/', $video->file);
        });
        return response()->json([
            'success' => true,
            'html' => view('website.pages.gallery.video', ['videos' => $videos, 'folder' => $folder])->render(),
            'files' => $videos
        ]);
    }

    function increaseView($fileId)
    {
        $file = FolderFile::find($fileId);
        $file->increment('view_count');
        return response()->json(['success' => true, 'view_count' => $file->view_count]);
    }

    function shareEventImage(Request $request)
    {
        $data = $request->all();
        $setting = getSetting();
        $settingId = $setting['image_setting_id'];
        if (!checkImageConfig($setting['image']))
            throw ValidationException::withMessages(['success' => false, 'message' => "Image uploading is not allowed, please check bunny setting"]);

        try {
            $event = Event::find($data['event_id']);
            $guestFolder = $event->folders()->where('folder_name', 'Guest Upload')->first();
            $this->prepareImageData($data, $guestFolder, $settingId, $event);
            $path = $this->uploadImageToBunny($guestFolder, $data);
            if (!$path['success']) {
                return response()->json(['success' => false, 'message' => $path['message']], 400);
            }
            $data['file'] = $path['path'];
            unset($data['event_id']);
            FolderFile::create($data);
            return response()->json(['success' => true, 'message' => 'Image has been uploaded successfully']);
        } catch (Throwable $e) {
            createServerError($e, "updateFile", "files");
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    function prepareImageData(&$data, $guestFolder, $settingId, $event)
    {
        $data['folder_id'] = $guestFolder->id;
        $data['file_type'] = $guestFolder->folder_type;
        $data['setting_id'] = $settingId;
        $data['file_status'] = $event->supportAutoApprove() ? "approved" : "pending";
        $fileNameWithExtension = $data['file']->getClientOriginalName();
        $fileName = pathinfo($data['file']->getClientOriginalName(), PATHINFO_FILENAME);
        $data['file_name'] = $fileName;
        $data['file_name_with_extension'] = $fileNameWithExtension;
    }

    function uploadImageToBunny($guestFolder, $data)
    {
        $folder = EventFolder::find($guestFolder->id);
        $bunnyMainFolderName = $folder->event->bunny_main_folder_name;
        $bunnyEventFolderName = $folder->event->bunny_event_name;
        $path = $bunnyMainFolderName . '/' . $bunnyEventFolderName . '/' . $folder->bunny_folder_name . '/' . $data['file_name_with_extension'];
        return $this->bunnyService->GuarantiedUploadImage($data['file'], $path);
    }

    function deleteImage($id)
    {
        try {
            $file = FolderFile::find($id);
            if (Auth::user()->id != $file->created_by)
                return response()->json(['success' => false, 'message' => "You don't have permission to delete this image"]);
            if ($file->file_type == 'video')
                $file->file_bunny_id ? $this->bunnyVideoService->deleteVideo($file->file_bunny_id) : null;
            else if ($file->file_type == 'image')
                $this->bunnyService->deleteFile($file);
            $file->delete();
            return response()->json(['success' => true, 'message' => 'Image has been deleted successfully']);
        } catch (Throwable $th) {
            createServerError($th, "deleteFile", "files");
            return response()->json(['success' => true, 'message' => 'Error happen during image deletion']);
        }
    }

    function bunnyVideoWebhook(Request $request)
    {
        $data = $request->all();
        $video = FolderFile::where('file_bunny_id', $data['VideoGuid'])->first();
        $video->bunny_status = $data['Status'];
        $video->save();
    }

    function getStatusStringFromCode($code)
    {
        $data = [
            0 => "Queued",
            1 => "Processing",
            2 => "Encoding",
            3 => "Finished",
            4 => "Resolution",
            5 => "Failed",
            6 => "PresignedUploadStarted",
            7 => "PresignedUploadFinished",
            8 => "PresignedUploadFailed",
            9 => "CaptionsGenerated",
            10 => "TitleOrDescriptionGenerated"
        ];
        return $data[$code];
    }

    function checkToken()
    {
        return response()->json(['success' => true]);
    }
}
