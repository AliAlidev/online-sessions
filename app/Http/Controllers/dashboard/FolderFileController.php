<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\events\files\CreateFileRequest;
use App\Http\Requests\events\files\UpdateFileRequest;
use App\Models\EventFolder;
use App\Models\FolderFile;
use App\Services\BunnyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FolderFileController extends Controller
{
    protected BunnyService $bunnyService;
    function __construct(BunnyService $bunnyService)
    {
        $this->bunnyService = $bunnyService;
    }

    function index(Request $request, $folderId, $folderType)
    {
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
                    elseif ($row->file_type == 'video')
                        return '<video src="' . asset($row->file) . '" data-type="' . $row->file_type . '" width="100px" height="100px" class="file-previewer"></video>';
                    else
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
    }

    function show($id)
    {
        $file = FolderFile::find($id);
        return response()->json(['success' => true, 'data' => $file]);
    }

    function changeStatus(Request $request)
    {
        $file = FolderFile::find($request->get('file_id'));
        $file->file_status = $request->get('file_status');
        $file->update();
        return response()->json(['success' => true, 'message' => 'File status has been updated successfully']);
    }

    function store(CreateFileRequest $request, $folderId, $folderType)
    {
        $data = $request->validated();
        $data['folder_id'] = $folderId;
        $data['file_type'] = $folderType;
        $folder = EventFolder::find($folderId);
        $path = $folder->folder_name . '/'. $data['file_name'];
        $path = $this->bunnyService->GuarantiedUploadFile($data['file'], $path);
        $data['file'] = $path;
        FolderFile::create($data);
        return response()->json(['success' => true, 'message' => 'File has been uploaded successfully']);
    }

    function update(UpdateFileRequest $request)
    {
        $data = $request->validated();
        $file = FolderFile::find($data['file_id']);
        $data['file'] = isset($data['file']) ? 'storage/' . uploadFile($data['file'], 'folder_files') : $file->file;
        unset($data['file_id']);
        $file->update($data);
        return response()->json(['success' => true, 'message' => 'File has been updated successfully']);
    }

    function delete($id)
    {
        $folder = FolderFile::find($id);
        $file = str_replace("storage/", "", $folder->file);
        deleteFile($file);
        $folder->delete();
        return response()->json(['success' => true, 'message' => 'File has been deleted successfully']);
    }
}
