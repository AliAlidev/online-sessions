<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\events\folders\CreateFolderRequest;
use App\Http\Requests\events\folders\UpdateFolderRequest;
use App\Models\Event;
use App\Models\EventFolder;
use App\Models\FolderFile;
use Illuminate\Support\Str;
use App\Services\BunnyImageService;
use App\Services\bunnyVideoService;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FolderController extends Controller
{
    protected BunnyImageService $bunnyService;
    protected BunnyVideoService $bunnyVideoService;
    function __construct(BunnyVideoService $bunnyVideoService, BunnyImageService $bunnyService)
    {
        $this->bunnyVideoService = $bunnyVideoService;
        $this->bunnyService = $bunnyService;
    }

    function index(Request $request, $eventId)
    {
        try {
            if ($request->ajax()) {
                $folders = Event::find($eventId)->folders;
                return DataTables::of($folders)
                    ->addColumn('actions', function ($folder) {
                        $row = '<a data-id="' . $folder->id . '" href="' . route('folders.update', $folder->id) . '" class="update-folder btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>
                                <a href="#" data-url="' . route('folders.delete', $folder->id) . '" class="delete-folder btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i></a>';
                        if ($folder->folder_type != "link")
                            $row .= ('<a title="Files" href="' . route('files.index', [$folder->id, $folder->folder_type]) . '" class="btn rounded-pill btn-icon btn-primary" style="margin-left:3px"><i class="bx bx-file" style="color:white"></i> </a>');
                        return $row;
                    })
                    ->addIndexColumn()
                    ->editColumn('event_id', function ($row) {
                        return $row->event->event_name;
                    })
                    ->editColumn('folder_thumbnail', function ($row) {
                        return $row->folder_thumbnail ? '<img src="/' . $row->folder_thumbnail . '" alt="" width="100px" height="100px">' : null;
                    })
                    ->editColumn('folder_link', function ($row) {
                        return $row->folder_type == "link" ? '<a target="_blank" class="btn btn-label-linkedin" href="' . $row->folder_link . '"> Link </a>' : null;
                    })
                    ->rawColumns(['folder_thumbnail', 'folder_link', 'actions'])
                    ->make(true);
            }
            return view('dashboard.folder.index');
        } catch (Exception $th) {
            createServerError($th, "indexFolder", "folders");
            return false;
        }
    }

    function show($id)
    {
        try {
            $folder = EventFolder::find($id)->append(['can_update_folder_name']);
            return response()->json(['success' => true, 'data' => $folder]);
        } catch (Exception $th) {
            createServerError($th, "showFolder", "folders");
            return false;
        }
    }

    function store(CreateFolderRequest $request, $eventId)
    {
        try {
            $data = $request->validated();
            $data['folder_thumbnail'] = isset($data['folder_thumbnail']) ? 'storage/' . uploadFile($data['folder_thumbnail'], 'folders/folder_thumbnail') : null;
            $data['event_id'] = $eventId;
            $data['folder_name'] = Str::slug($data['folder_name']);
            $data['bunny_folder_name'] = $data['folder_name'];
            EventFolder::create($data);
            return response()->json(['success' => true, 'message' => 'Folder has been created successfully']);
        } catch (Exception $th) {
            createServerError($th, "storeFolder", "folders");
            return false;
        }
    }

    function update(UpdateFolderRequest $request)
    {
        try {
            $data = $request->validated();
            $folder = EventFolder::find($data['folder_id']);
            $oldFolder = clone $folder;
            $hasThumbnail = $data['folder_thumbnail']??null;
            $data['folder_thumbnail'] = isset($data['folder_thumbnail']) ? 'storage/' . uploadFile($data['folder_thumbnail'], 'folders/folder_thumbnail') : $folder->folder_thumbnail;
            unset($data['folder_id']);
            $folder->update($data);
            if (isset($hasThumbnail)) {
                $folderThumbnail = str_replace("storage/", "", $oldFolder->folder_thumbnail);
                deleteFile($folderThumbnail);
            }

            return response()->json(['success' => true, 'message' => 'Folder has been updated successfully']);
        } catch (Exception $th) {
            createServerError($th, "updateFolder", "folders");
            return false;
        }
    }

    function delete($id)
    {
        try {
            $folder = EventFolder::find($id);
            $folderThumbnail = str_replace("storage/", "", $folder->folder_thumbnail);
            deleteFile($folderThumbnail);
            if ($folder->folder_type == "image") {
                $this->bunnyService->deleteFolder($folder->event->bunny_main_folder_name . '/' . $folder->event->event_name . '/' . $folder->bunny_folder_name);
            } else if ($folder->folder_type == "video") {
                $folder = EventFolder::find($id);
                $videos = $folder->files->pluck('file_bunny_id')->toArray();
                $this->bunnyVideoService->deleteMultipleVideos($videos);
            }

            $folder->delete();
            return response()->json(['success' => true, 'message' => 'Folder has been deleted successfully']);
        } catch (Exception $th) {
            createServerError($th, "deleteFolder", "folders");
            return false;
        }
    }
}
