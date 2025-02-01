<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\events\folders\CreateFolderRequest;
use App\Http\Requests\events\folders\UpdateFolderRequest;
use App\Models\Event;
use App\Models\EventFolder;
use Illuminate\Support\Str;
use App\Services\BunnyService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FolderController extends Controller
{
    protected BunnyService $bunnyService;
    function __construct(BunnyService $bunnyService)
    {
        $this->bunnyService = $bunnyService;
    }

    function index(Request $request, $eventId)
    {
        if ($request->ajax()) {
            $folders = Event::find($eventId)->folders;
            return DataTables::of($folders)
                ->addColumn('actions', function ($folder) {
                    return '<a data-id="' . $folder->id . '" href="' . route('folders.update', $folder->id) . '" class="update-folder btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt"></i></a>
                            <a href="#" data-url="' . route('folders.delete', $folder->id) . '" class="delete-folder btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>';
                })
                ->addIndexColumn()
                ->editColumn('folder_thumbnail', function ($row) {
                    return '<img src="/' . $row->folder_thumbnail . '" alt="" width="100px" height="100px">';
                })
                ->editColumn('folder_link', function ($row) {
                    return '<a target="_blank" class="btn btn-label-linkedin" href="' . $row->folder_link . '"> Link </a>';
                })
                ->rawColumns(['folder_thumbnail', 'folder_link', 'actions'])
                ->make(true);
        }
        return view('dashboard.folder.index');
    }

    function show($id)
    {
        $folder = EventFolder::find($id);
        return response()->json(['success' => true, 'data' => $folder]);
    }

    function store(CreateFolderRequest $request, $eventId)
    {
        $data = $request->validated();
        $data['folder_thumbnail'] = isset($data['folder_thumbnail']) ? 'storage/' . uploadFile($data['folder_thumbnail'], 'folder_thumbnail') : null;
        $data['event_id'] = $eventId;
        $fileName = Str::slug($data['folder_name']);
        $data['bunny_cdn_link'] = config('services.bunny.cdn_pull_zone') . '.b-cdn.net/' . $fileName;
        $data['bunny_link'] = config('services.bunny.region') . '.bunnycdn.com/' . config('services.bunny.storage_zone') . '/' . $fileName;
        EventFolder::create($data);
        $this->bunnyService->createFolder($fileName);
        return response()->json(['success' => true, 'message' => 'Folder has been created successfully']);
    }

    function update(UpdateFolderRequest $request)
    {
        $data = $request->validated();
        $folder = EventFolder::find($data['folder_id']);
        $data['folder_thumbnail'] = isset($data['folder_thumbnail']) ? 'storage/' . uploadFile($data['folder_thumbnail'], 'folder_thumbnail') : $folder->folder_thumbnail;
        unset($data['folder_id']);
        $this->bunnyService->renameFolder($folder->folder_name, Str::slug($data['folder_name']));
        $folder->update($data);
        return response()->json(['success' => true, 'message' => 'Folder has been updated successfully']);
    }

    function delete($id)
    {
        $folder = EventFolder::find($id);
        $folderThumbnail = str_replace("storage/", "", $folder->folder_thumbnail);
        deleteFile($folderThumbnail);
        $folder->delete();
        return response()->json(['success' => true, 'message' => 'Folder has been deleted successfully']);
    }
}
