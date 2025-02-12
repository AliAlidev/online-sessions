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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    function index(Request $request, $folderId, $folderType)
    {
        try {
            if ($request->ajax()) {
                $folders = EventFolder::find($folderId)->files;
                return DataTables::of($folders)
                    ->addColumn('actions', function ($folder) {
                        return '<a data-id="' . $folder->id . '" href="' . route('files.update', $folder->id) . '" class="update-file btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>
                                <a href="#" data-url="' . route('files.delete', $folder->id) . '" class="delete-file btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>';
                    })
                    ->addIndexColumn()
                    ->editColumn('file', function ($row) {
                        if ($row->file_type == 'image')
                            return '<img src="' . asset($row->file) . '" data-type="' . $row->file_type . '" alt="" width="100px" height="100px" class="file-previewer">';
                        elseif ($row->file_type == 'video') {
                            return '<iframe
                                    src="' . $this->bunnyVideoService->getEmbedUrl($row->file_bunny_id) . '?autoplay=false&muted=true"
                                    width="200"
                                    height="150"
                                    frameborder="0"
                                    allow="encrypted-media"
                                    allowfullscreen>
                                </iframe>';
                        } else
                            return '';
                    })
                    ->addColumn('name_and_size', function ($row) {
                        $name = '<span style="display:block">' . $row->file_name . '</span>';
                        $name .= '<span style="display:block">' . $row->file_size . ' MB' . '</span>';
                        return $name;
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->file_status == "pending")
                            return '<span class="badge bg-label-info file-status-modal" data-status="pending" data-id="' . $row->id . '">Pending</span>';
                        else if ($row->file_status == "approved")
                            return '<span class="badge bg-label-success file-status-modal" data-status="approved" data-id="' . $row->id . '">Approved</span>';
                        else if ($row->file_status == "rejected")
                            return '<span class="badge bg-label-danger file-status-modal" data-status="rejected" data-id="' . $row->id . '">Rejected</span>';
                    })
                    ->addColumn('date', function ($row) {
                        return Carbon::parse($row->date)->format('d/m/Y');
                    })
                    ->rawColumns(['file', 'status', 'name_and_size', 'actions'])
                    ->make(true);
            }
            return view('dashboard.files.index', ['folderType' => $folderType]);
        } catch (Exception $th) {
            createServerError($th, "indexFile", "files");
            return false;
        }
    }

    function show($id)
    {
        try {
            $file = FolderFile::find($id);
            return response()->json(['success' => true, 'data' => $file]);
        } catch (Exception $th) {
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
        } catch (Exception $th) {
            createServerError($th, "changeStatus", "files");
            return false;
        }
    }

    // Upload video to server
    public function uploadFile(Request $request)
    {
        try {
            $type = $request->get('folder_type', 'image');
            $request->validate([
                'file' => $type == 'image' ? 'required|mimes:jpeg,png,jpg,webp|max:10000' : 'required|mimes:mp4|max:50000'
            ]);

            // Store the uploaded video temporarily
            $file = $request->file('file');
            $fileNameWithExtension = $file->getClientOriginalName();
            $fileNameWithoutExtension = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            $filePath = uploadFile($file, 'files_temp');

            // Get the file size
            $fileSize = filesize(storage_path('app/public/' . $filePath));  // Assuming the file is stored in storage/app/public

            return response()->json([
                'message' => 'Video uploaded to server. Starting BunnyCDN upload...',
                'file_path' => url('/') . '/storage/' . $filePath,
                'file_name' => $fileNameWithoutExtension,
                'file_name_with_extension' => $fileNameWithExtension,
                'upload_id' => uniqid() . rand(1000, 9999) . Auth::user()->id, // Use this ID for polling progress
                'file_size' => $fileSize // Return file size in bytes
            ]);
        } catch (Exception $th) {
            createServerError($th, "uploadFile", "files");
            return false;
        }
    }

    // Upload video to BunnyCDN
    public function store(CreateFileRequest $request, $folderId, $folderType)
    {
        $data = $request->validated();
        $fileMainPath = $data['file_path'];
        $settingId = null;
        if ($folderType == "image") {
            $setting = getSetting();
            $settingId = $setting['image_setting_id'];
            if (!checkImageConfig($setting['image']))
                throw new Exception("Image uploading is not allowed, please check bunny setting");
        }

        if ($folderType == "video") {
            $setting = getSetting();
            $settingId = $setting['video_setting_id'];
            if (!checkVideoConfig($setting['video']))
                throw new Exception("Video uploading is not allowed, please check bunny setting");
        }

        try {
            $data['folder_id'] = $folderId;
            $data['file_type'] = $folderType;
            $data['file_status'] = "approved";
            $folder = EventFolder::find($folderId);
            if ($folder->folder_type == 'image') {
                $bunnyMainFolderName = $folder->event->bunny_main_folder_name;
                $bunnyEventFolderName = $folder->event->bunny_event_name;
                $path = $bunnyMainFolderName . '/' . $bunnyEventFolderName . '/' . $folder->bunny_folder_name . '/' . $data['file_name_with_extension'];
                $path = $this->bunnyService->GuarantiedUploadImage($data['file_path'], $path, $data['upload_id'], $data['file_size']);
                if (!$path['success']) {
                    return response()->json(['success' => false, 'message' => $path['message']], 400);
                }
                $data['file'] = $path['path'];
            } else if ($folder->folder_type == 'video') {
                $path = $this->bunnyVideoService->guarantiedUploadVideo($data['file_path'], $data['file_name'], $data['upload_id'], $data['file_size']);
                if (!$path['success']) {
                    return response()->json(['success' => false, 'message' => $path['message']], 400);
                }
                $data['file'] = $path['path'];
                $data['file_bunny_id'] = $path['guid'];
            }
            unset($data['file_path']);
            unset($data['upload_id']);
            unset($data['file_size']);
            $data['setting_id'] = $settingId;
            FolderFile::create($data);
            $fileMainPath = str_replace(url('/') . '/storage/', "", $fileMainPath);
            deleteFile($fileMainPath);
            return response()->json(['success' => true, 'message' => 'File has been uploaded successfully']);
        } catch (Exception $e) {
            createServerError($e, "updateFile", "files");
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadedFileStatus($uploadId)
    {
        return response()->json([
            'progress' => Cache::get("upload_progress_" . $uploadId, 0)
        ]);
    }

    function update(UpdateFileRequest $request)
    {
        try {
            $data = $request->validated();
            $fileMainPath = $data['file_path'];
            Cache::put("upload_progress_" . $data['upload_id'], 0, 600); // Store initial progress (0%) for 30 minutes
            $file = FolderFile::find($data['file_id']);
            $folderType = $file->folder->folder_type;
            $settingId = null;
            if ($folderType == "image") {
                $setting = getSetting();
                $settingId = $setting['image_setting_id'];
                if (!checkImageConfig($setting['image']))
                    throw new Exception("Image uploading is not allowed, please check bunny setting");
            }

            if ($folderType == "video") {
                $setting = getSetting();
                $settingId = $setting['image_setting_id'];
                if (!checkVideoConfig($setting['video']))
                    throw new Exception("Video uploading is not allowed, please check bunny setting");
            }

            if ($folderType == 'image') {
                if (isset($data['file_name'])) {
                    $bunnyMainFolderName = $file->folder->event->bunny_main_folder_name;
                    $bunnyEventFolderName = $file->folder->event->bunny_event_name;
                    $path = $bunnyMainFolderName . '/' . $bunnyEventFolderName . '/' . $file->folder->bunny_folder_name . '/' . $data['file_name_with_extension'];
                    $path = $this->bunnyService->GuarantiedUploadImage($data['file_path'], $path, $data['upload_id'], $data['file_size']);
                    if (!$path['success']) {
                        return response()->json(['success' => false, 'message' => $path['message']], 400);
                    }
                    $this->bunnyService->deleteFile($file);
                    $data['file'] = $path['path'];
                }
            } else if ($folderType == 'video') {
                if (isset($data['file_name'])) {
                    $path = $this->bunnyVideoService->guarantiedUploadVideo($data['file_path'], $data['file_name'], $data['upload_id'], $data['file_size']);
                    if (!$path['success'])
                        return response()->json(['success' => false, 'message' => 'Error happen during video uploading']);
                    $this->bunnyVideoService->deleteVideo($file->file_bunny_id);
                    $data['file'] = $path['path'];
                    $data['file_bunny_id'] = $path['guid'];
                }
            }
            $data['file'] = isset($data['file']) ? $data['file'] : $file->file;
            unset($data['file_id']);
            unset($data['file_path']);
            unset($data['upload_id']);
            $data['setting_id'] = $settingId;
            $file->update($data);
            $fileMainPath = str_replace(url('/') . '/storage/', "", $fileMainPath);
            deleteFile($fileMainPath);
            return response()->json(['success' => true, 'message' => 'File has been updated successfully']);
        } catch (Exception $th) {
            createServerError($th, "updateFile", "files");
            return false;
        }
    }

    function updateWithoutFile(Request $request)
    {
        try {
            $data = $request->validate([
                'file_id' => 'required',
                'user_name' => 'required',
                'description' => 'nullable',
                'file_status' => 'required'
            ]);

            $file = FolderFile::find($data['file_id']);
            unset($data['file_id']);
            $file->update($data);
            return response()->json(['success' => true, 'message' => 'File has been updated successfully']);
        } catch (Exception $th) {
            createServerError($th, "updateWithoutFile", "files");
            return false;
        }
    }

    function delete($id)
    {
        try {
            $file = FolderFile::find($id);
            if ($file->file_type == 'video')
                $file->file_bunny_id ? $this->bunnyVideoService->deleteVideo($file->file_bunny_id) : null;
            else if ($file->file_type == 'image')
                $this->bunnyService->deleteFile($file);
            $file->delete();
            return response()->json(['success' => true, 'message' => 'File has been deleted successfully']);
        } catch (Exception $th) {
            createServerError($th, "deleteFile", "files");
            return false;
        }
    }
}
