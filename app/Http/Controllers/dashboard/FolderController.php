<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Folder;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FolderController extends Controller
{
    function index(Request $request, $eventId)
    {
        if ($request->ajax()) {
            $folders = Event::find($eventId)->folders;
            return DataTables::of($folders)
                ->addColumn('actions', function ($folder) {
                    return '<a data-id="' . $folder->id . '" href="' . route('folders.edit', $folder->id) . '" class="update-folder btn btn-icon btn-outline-primary"><i class="bx bx-edit-alt"></i></a>
                            <a href="#" data-url="' . route('folders.delete', $folder->id) . '" class="delete-folder btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>';
                })
                ->addIndexColumn()
                ->rawColumns(['actions'])
                ->make(true);
        }
        return view('dashboard.folder.index');
    }
}
