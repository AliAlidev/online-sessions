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
        $month = $request->route('month');
        $eventSlug = $request->route('event_slug');
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.index', ['year' => $year, 'month' => $month, 'event_slug' => $eventSlug, 'event' => $event]);
    }

    function galleryRedirectUrl(Request $request)
    {
        $event = Event::where('bunny_event_name', $request->event_slug)->first();
        $wantsPassword = false;
        $url = route('landing.gallery', ['year' => $request->year, 'month' => $request->month, 'event_slug' => $request->event_slug]);
        if ($event->event_password != null) {
            $url = route('landing.event_password', ['year' => $request->year, 'month' => $request->month, 'event_slug' => $request->event_slug]);
            $wantsPassword = true;
        }

        return response()->json([
            'success' => true,
            'wantsPassword' => $wantsPassword,
            'url' => $url,
            'year' => $request->year,
            'month' => $request->month,
            'event_slug' => $request->event_slug
        ]);
    }

    function eventPassword(Request $request)
    {
        return view('website.pages.event_password', ['year' => $request->year, 'month' => $request->month, 'event_slug' => $request->event_slug]);
    }

    function applyEventPassword(Request $request)
    {
        $data = $request->all();
        $eventSlug = $data['event_slug'];
        $password = $data['password'];
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        if (Hash::check($password, $event->event_password))
            return response()->json([
                'success' => true,
                'url' => route('landing.gallery', ['year' => $data['year'], 'month' => $data['month'], 'event_slug' => $data['event_slug']])
            ]);
        return response()->json(['success' => false, 'message' => 'Invalid password']);
    }

    function shareRedirectUrl(Request $request)
    {
        return response()->json([
            'success' => true,
            'url' => route('landing.share', ['year' => $request->year, 'month' => $request->month, 'event_slug' => $request->event_slug]),
            'year' => $request->year,
            'month' => $request->month,
            'event_slug' => $request->event_slug
        ]);
    }

    function gallery(Request $request)
    {
        $year = $request->route('year');
        $month = $request->route('month');
        $eventSlug = $request->route('event_slug');
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        $foldersList = [];
        $event->folders->map(function ($folder) use (&$foldersList, $event) {
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
            if ($folder->folder_type == 'link')
                $foldersList[] = $folder;
        });
        return view('website.pages.gallery.gallery', ['year' => $year, 'month' => $month, 'event_slug' => $eventSlug, 'event' => $event, 'folders' => $foldersList]);
    }

    function image(Request $request)
    {
        $folderId = $request->folder_id;
        $folder = EventFolder::find($folderId);
        $images = $folder->files()->where('file_status', 'approved')->get();
        $eventSupportDownload = $folder->event->supportImageDownload();
        return response()->json([
            'success' => true,
            'html' => view('website.pages.gallery.image', ['images' => $images])->render(),
            'eventSupportDownload' => $eventSupportDownload
        ]);
    }

    function share(Request $request)
    {
        $year = $request->route('year');
        $month = $request->route('month');
        $eventSlug = $request->route('event_slug');
        $event = Event::where('bunny_event_name', $eventSlug)->first();
        $event->start_date = Carbon::parse($event->start_date)->format('d/m/Y');
        return view('website.pages.share', ['year' => $year, 'month' => $month, 'event_slug' => $eventSlug, 'event' => $event]);
    }

    function video(Request $request)
    {
        $folderId = $request->folder_id;
        $folder = EventFolder::find($folderId);
        $videos = $folder->files->where('file_status', 'approved');
        return response()->json([
            'success' => true,
            'html' => view('website.pages.gallery.video', ['videos' => $videos])->render(),
            'files' => $videos
        ]);
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
            return response()->json(['success' => true, 'message' => 'File has been uploaded successfully']);
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
}
