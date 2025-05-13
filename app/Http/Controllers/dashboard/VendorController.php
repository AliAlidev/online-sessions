<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\vendors\CreateVendorRequest;
use App\Http\Requests\vendors\UpdateVendorRequest;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class VendorController extends Controller
{
    function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $vendors = Vendor::orderBy('created_at','desc')->get();
                return DataTables::of($vendors)
                    ->addColumn('actions', function ($vendor) {
                        $actions = '';
                        Auth::user()->hasPermissionTo('update_vendor') ? $actions .= '<a data-id="' . $vendor->id . '" href="' . route('vendors.edit', $vendor->id) . '" class="update-vendor btn btn-icon btn-outline-primary m-1"><i class="bx bx-edit-alt" style="color:#696cff"></i></a>' : '';
                        Auth::user()->hasPermissionTo('delete_vendor') ? $actions .= '<a href="#" data-url="' . route('vendors.delete', $vendor->id) . '" class="delete-vendor btn btn-icon btn-outline-primary"><i class="bx bx-trash" style="color:red"></i> </a>' : '';
                        return $actions;
                    })
                    ->editColumn('contact_button_link', function ($row) {
                        return '<a target="_blank" class="btn btn-label-linkedin" href="' . $row->contact_button_link . '"> Link </a>';
                    })
                    ->editColumn('logo', function ($row) {
                        return $row->logo ? '<img src="/' . $row->logo . '" alt="" width="100px" height="100px">' : null;
                    })
                    ->addIndexColumn()
                    ->rawColumns(['contact_button_link', 'logo', 'actions'])
                    ->make(true);
            }
            return view('dashboard.vendor.index');
        } catch (Exception $th) {
            createServerError($th, "indexVendor", "vendors");
            return false;
        }
    }

    function create()
    {
        try {
            return view('dashboard.vendor.create');
        } catch (Exception $th) {
            createServerError($th, "createVendor", "vendors");
            return false;
        }
    }

    function edit($id)
    {
        try {
            $vendor = Vendor::find($id);
            return view('dashboard.vendor.update', [ 'vendor' => $vendor]);
        } catch (Exception $th) {
            createServerError($th, "editVendor", "vendors");
            return false;
        }
    }

    function store(CreateVendorRequest $request)
    {
        try {
            $data = $request->validated();
            $data['logo'] = $request->hasFile('logo') ? 'storage/' . uploadFile($request->file('logo'), 'vendors/vendor_logo') : null;
            Vendor::create($data);
            session()->flash('success', 'Vendor has been created successfully');
            return response()->json(['success' => true, 'url' => route('vendors.index')]);
        } catch (Exception $th) {
            createServerError($th, "storeVendor", "vendors");
            return false;
        }
    }

    function update(UpdateVendorRequest $request)
    {
        try {
            $data = $request->validated();
            $vendor = Vendor::find($data['vendor_id']);
            $oldVendor = clone $vendor;
            $data['logo'] = $request->hasFile('logo') ? 'storage/' . uploadFile($request->file('logo'), 'vendors/vendor_logo') : $vendor->logo;
            unset($data['vendor_id']);
            $vendor->update($data);
            // remove old images
            if($request->hasFile('logo')){
                $logo = str_replace("storage/", "", $oldVendor->logo);
                deleteFile($logo);
            }
            if($request->hasFile('profile_picture')){
                $profilePicture = str_replace("storage/", "", $oldVendor->profile_picture);
                deleteFile($profilePicture);
            }
            session()->flash('success', 'Vendor has been updated successfully');
            return response()->json(['success' => true, 'url' => route('vendors.edit', $vendor->id)]);
        } catch (Exception $th) {
            createServerError($th, "updateVendor", "vendors");
            return false;
        }
    }

    function delete($id)
    {
        try {
            $vendor = Vendor::find($id);
            $logo = str_replace("storage/", "", $vendor->logo);
            deleteFile($logo);
            $vendor->delete();
            $count = Vendor::count();
            session()->flash('success', 'Vendor has been deleted successfully');
            return response()->json(['success' => true, 'count' => $count]);
        } catch (Exception $th) {
            createServerError($th, "deleteVendor", "vendors");
            return false;
        }
    }
}
