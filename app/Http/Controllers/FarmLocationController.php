<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FarmLocation as FL;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class FarmLocationController extends Controller
{
    /**
     * Withdrawal Series
     */
    public function farmLocation()
    {
        return view('farm-loc.farm-location');
    }

    public function showFarmLocation($id = null)
    {
        return view('farm-loc.farm-location-update', ['farm_location_id' => GC::decryptString($id)]);
    }

    /**
     * flList
     * @param   Request $request
     * @return   view('farm-loc.farm-location-table');
     */
    public function flList(Request $request)
    {
        if($request->ajax()) {

            $farm_loc = FL::where('active_status', 1)->get();
            $data = collect();

            if($farm_loc->count() > 0) {
                $ctr = 1;
                foreach($farm_loc as $al) {
                    $data->push([
                        'id' => $ctr,
                        'location' => strtoupper($al->farm_location),
                        'abbreviation' => strtoupper($al->abbreviation),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'farmloc_edit') ? 
                                '<a href="' . route('farm.location.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'farmloc_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'FarmLocation', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'farmloc_edit') == false && ACC::checkAccess(Auth::id(), 'farmloc_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('farm-loc.farm-location-table');
    }

    public function wsRemoveFilter()
    {
        $farm_location = FL::where('active_status', 1)->get();
        $data = collect();

        if($farm_location->count() > 0) {
            $ctr = 1;
            foreach($farm_location as $al) {
                $data->push([
                    'id' => $ctr,
                    'location' => strtoupper($al->farm_location),
                    'abbreviation' => strtoupper($al->abbreviation),
                    'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                    'action' => '<button class="btn btn-success btn-sm" disabled>N/A</button>'
                ]);
                $ctr++;
            }
        }
        return DataTables::of($data)
                ->rawColumns(['action'])
                ->make(true);
    }

    /**
     * wsLocationOrDepartment
     * This Function is for Filtering the table using Location and Department
     * @param   $field_name, $field_value
     * @return   DataTables::of($data)->rawColumns(['action'])->make(true);
     */
    public function flLocation($field_name = null, $field_value = null)
    {
        $data = collect();

        $assigned_list = FL::where('active_status', 1)
            ->where($field_name == 'location' ? "farm_location" : "department_division", $field_value)
            ->get();

        if ($assigned_list->count() > 0) {
            foreach ($assigned_list as $key => $al) {
                $data->push([
                    'id' => $key + 1,
                    'location' => strtoupper($al->farm_location),
                    'abbreviation' => strtoupper($al->abbreviation),
                    'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                    'action' => '<button class="btn btn-success btn-sm" disabled>N/A</button>'
                ]);
            }
        }

        return DataTables::of($data)->rawColumns(['action'])->make(true);
    }

    public function flAll($field_location = null)
    {

        $farm_location = FL::where('active_status', 1)
            ->where('farm_location', $field_location)
            ->get();

        $data = collect();
        foreach ($farm_location as $key => $al) {
            $data->push([
                'id' => $key + 1,
                'location' => strtoupper($al->farm_location),
                'abbreviation' => strtoupper($al->abbreviation),
                'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                'action' => '<button class="btn btn-success btn-sm" disabled>N/A</button>'
            ]);
        }

        return DataTables::of($data)->rawColumns(['action', 'status'])->make(true);

    }
}
