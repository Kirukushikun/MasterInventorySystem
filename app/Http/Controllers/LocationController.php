<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Location as LC;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class LocationController extends Controller
{
    public function locationDiv()
    {
        return view('location.location');
    }

    public function showLocationDiv($id)
    {
        return view('location.location-update', ['location_id' => GC::decryptString($id)]);
    }

    public function locationList(Request $request)
    {
        if($request->ajax()) {

            $location = LC::where('active_status', 1)->get();
            $data = collect();

            if($location->count() > 0) {
                $ctr = 1;
                foreach($location as $al) {
                    $data->push([
                        'id' => $ctr,
                        'title' => strtoupper($al->location_name),
                        'description' => strtoupper($al->description),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'location_edit') ? 
                                '<a href="' . route('location.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'location_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'Location', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'location_edit') == false && ACC::checkAccess(Auth::id(), 'location_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('location.location-table');
    }
}
