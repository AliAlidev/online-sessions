<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\events\files\CreateFileRequest;
use App\Http\Requests\events\files\UpdateFileRequest;
use App\Models\EventFolder;
use App\Models\FolderFile;
use App\Services\BunnyImageService;
use App\Services\BunnyVideoService;
use Carbon\Carbon;
use getID3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;
use Yajra\DataTables\DataTables;

class FolderFileController extends Controller
{
    protected BunnyImageService $bunnyService;
    protected BunnyVideoService $bunnyVideoService;

    function __construct(BunnyImageService $bunnyService, BunnyVideoService $bunnyVideoService)
    {
        $this->bunnyService = $bunnyService;
        $this->bunnyVideoService = $bunnyVideoService;
    }

    function index(Request $request, $eventSlug, $folderSlug)
    {
        try {
            $folder = EventFolder::whereHas('event', function ($qrt) use ($eventSlug) {
                $qrt->where('bunny_event_name', $eventSlug);
            })->where('bunny_folder_name', $folderSlug)->first();
            if ($request->ajax()) {
                $files = $folder->files()->orderBy('created_at', 'desc')->get();
                return DataTables::of($files)
                    ->addColumn('actions', function ($file) use ($folder, $folderSlug) {
                        $action = '';
                        ($file->file_type == 'image' && Auth::user()->hasPermissionTo('delete_image')) || ($file->file_type == 'video' && Auth::user()->hasPermissionTo('delete_video')) ? $action .= '<a href="#" data-url="' . route('files.delete', [$folder->event->bunny_event_name, $folderSlug, $file->id]) . '" class="delete-file btn btn-icon btn-outline-primary m-1"><i class="bx bx-trash" style="color:red"></i> </a>' : '';
                        ($file->file_type == 'image' && Auth::user()->hasPermissionTo('update_image')) || ($file->file_type == 'video' && Auth::user()->hasPermissionTo('update_video')) ? $action .= '<a data-id="' . $file->id . '" href="' . route('files.update', [$folder->event->bunny_event_name, $folderSlug, $file->id]) . '" class="update-file btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>' : '';
                        return $action;
                    })
                    ->addIndexColumn()
                    ->editColumn('file', function ($row) {
                        if ($row->file_type == 'image') {
                            return '<a href="'.asset($row->file).'" data-fancybox="preview-gallery" data-caption="Your caption here">
                                    <img class="file-previewer" src="'.asset($row->file).'" data-type="image" style="width: 100px;height: 100px">
                                </a> ';
                        } elseif ($row->file_type == 'video') {
                            return '<a href="' . $this->bunnyVideoService->getEmbedUrl($row->file_bunny_id) . '" target="_blank" class="link"><i class="bx bx-video 2xl" style="font-size: 30px;"></i> </a>';
                        } else
                            return '';
                    })
                    ->addColumn('name_and_size', function ($row) {
                        $name = '<span style="display:block">' . $row->file_name . '</span>';
                        $name .= '<span style="display:block">' . formatBytes($row->file_size) . '</span>';
                        return $name;
                    })
                    ->addColumn('status', function ($file) {
                        $acceptanceWord = '';
                        if (($file->file_type == 'image' && Auth::user()->hasPermissionTo('approve_decline_image')) || ($file->file_type == 'video' && Auth::user()->hasPermissionTo('approve_decline_video'))) {
                            $acceptanceWord = 'file-status-modal';
                        }
                        if ($file->file_status == "pending")
                            return '<span class="badge bg-label-info ' . $acceptanceWord . '" data-status="pending" data-id="' . $file->id . '">Pending</span>';
                        else if ($file->file_status == "approved")
                            return '<span class="badge bg-label-success ' . $acceptanceWord . '" data-status="approved" data-id="' . $file->id . '">Approved</span>';
                        else if ($file->file_status == "rejected")
                            return '<span class="badge bg-label-danger ' . $acceptanceWord . '" data-status="rejected" data-id="' . $file->id . '">Rejected</span>';
                    })
                    ->addColumn('date', function ($file) {
                        return Carbon::parse($file->date)->format('d/m/Y');
                    })
                    ->rawColumns(['file', 'status', 'name_and_size', 'actions'])
                    ->make(true);
            }
            return view('dashboard.files.index', ['folderType' => $folder->folder_type]);
        } catch (Throwable $th) {
            createServerError($th, "indexFile", "files");
            return false;
        }
    }

    function show($eventSlug, $folderSlug, $id)
    {
        try {
            $file = FolderFile::find($id);
            return response()->json(['success' => true, 'data' => $file]);
        } catch (Throwable $th) {
            createServerError($th, "showFile", "files");
            return false;
        }
    }

    function changeStatus(Request $request)
    {
        try {
            $file = FolderFile::find($request->get('file_id'));
            $file->file_status = $request->get('file_status');
            $file->update();
            return response()->json(['success' => true, 'message' => 'File status has been updated successfully']);
        } catch (Throwable $th) {
            createServerError($th, "changeStatus", "files");
            return false;
        }
    }

    public function store(CreateFileRequest $request, $eventSlug, $folderSlug)
    {
        $data = $request->validated();
        $folder = EventFolder::whereHas('event', function ($qrt) use ($eventSlug) {
            $qrt->where('bunny_event_name', $eventSlug);
        })->where('bunny_folder_name', $folderSlug)->first();

        $settingId = null;
        if ($folder->folder_type == "image" && !config('services.demo_mode')) {
            $setting = getSetting();
            $settingId = $setting['image_setting_id'];
            if (!checkImageConfig($setting['image']))
                throw ValidationException::withMessages(['success' => false, 'message' => "Image uploading is not allowed, please check bunny setting"]);
        }

        if ($folder->folder_type == "video" && !config('services.demo_mode')) {
            $setting = getSetting();
            $settingId = $setting['video_setting_id'];
            if (!checkVideoConfig($setting['video']))
                throw ValidationException::withMessages(['success' => false, 'message' => "Video uploading is not allowed, please check bunny setting"]);
        }

        try {
            $data['folder_id'] = $folder->id;
            $data['file_type'] = $folder->folder_type;
            $data['setting_id'] = $settingId;
            $data['file_status'] = "approved";
            $fileNameWithExtension = $data['file']->getClientOriginalName();
            $fileName = pathinfo($data['file']->getClientOriginalName(), PATHINFO_FILENAME);
            $data['file_name'] = $fileName;
            $data['file_name_with_extension'] = $fileNameWithExtension;
            if ($folder->folder_type == 'image') {
                $bunnyMainFolderName = $folder->event->bunny_main_folder_name;
                $bunnyEventFolderName = $folder->event->bunny_event_name;
                $path = $bunnyMainFolderName . '/' . $bunnyEventFolderName . '/' . $folder->bunny_folder_name . '/' . $fileNameWithExtension;
                $path = $this->bunnyService->GuarantiedUploadImage($data['file'], $path);
                if (!$path['success']) {
                    return response()->json(['success' => false, 'message' => $path['message']], 400);
                }
                $data['file'] = $path['path'];
            } else if ($folder->folder_type == 'video') {
                $path = $this->bunnyVideoService->guarantiedUploadVideo($data['file'], $fileName, $folder->event->video_collection_id, $data['video_resolution']);
                if (!$path['success']) {
                    return response()->json(['success' => false, 'message' => $path['message']], 400);
                }
                $data['video_duration'] = $this->getVideoDuration($data['file']);
                $data['file'] = $path['path'];
                $data['file_bunny_id'] = $path['guid'];
                $data['thumbnail_url'] = "https://" . $setting['video']['stream_pull_zone'] . ".b-cdn.net/" . $path['guid'] . "/preview.webp";
            }
            FolderFile::create($data);
            return response()->json(['success' => true, 'message' => 'File has been uploaded successfully']);
        } catch (Throwable $e) {
            createServerError($e, "updateFile", "files");
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    function getVideoDuration($video)
    {
        $filePath = $video->getPathname();
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($filePath);
        $durationInSeconds = $fileInfo['playtime_seconds'] ?? 0;
        $minutes = floor($durationInSeconds / 60);
        $seconds = $durationInSeconds % 60;
        $formattedDuration = "{$minutes} Min {$seconds} Sec";
        return $formattedDuration;
    }

    function update(UpdateFileRequest $request, $eventSlug, $folderSlug)
    {
        $data = $request->validated();
        $file = FolderFile::find($data['file_id']);
        $folder = EventFolder::find($file->folder_id);
        try {
            $data['folder_id'] = $folder->folder_id;
            $data['file_type'] = $folder->folder_type;
            $data['setting_id'] = $file->setting_id;
            $data['file'] = $file->file;
            unset($data['file_id']);
            unset($data['folder_id']);
            $file->update($data);
            return response()->json(['success' => true, 'message' => 'File has been updated successfully']);
        } catch (Throwable $th) {
            createServerError($th, "updateFile", "files");
            return response()->json(['success' => false, 'message' => $th->getMessage()], 400);
        }
    }

    function delete($eventSlug, $folderSlug, $id)
    {
        try {
            $file = FolderFile::find($id);
            if ($file->file_type == 'video')
                $file->file_bunny_id ? $this->bunnyVideoService->deleteVideo($file->file_bunny_id) : null;
            else if ($file->file_type == 'image')
                $this->bunnyService->deleteFile($file);
            $file->delete();
            return response()->json(['success' => true, 'message' => 'File has been deleted successfully']);
        } catch (Throwable $th) {
            createServerError($th, "deleteFile", "files");
            return false;
        }
    }
}
