<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;
use App\Models\GatepassSeries as GS;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class GatepassSeriesController extends Controller
{
    /**
     * gatepass Series
     */
    public function gatepassSeries()
    {
        return view('GatepassSeries.gatepass-series');
    }

    public function showWithrawalSeries($id)
    {
        return view('GatepassSeries.gatepass-series-update', ['gate_series_id' => GC::decryptString($id)]);
    }

    public static function getValueById($id, $action)
    {

        if($action == "dep"){
            return DepartmentDivision::where('id', $id)->first()->department_division;
        }
        else{
            return FarmLocation::where('id', $id)->first()->farm_location;
        }
    }

    /**
     * GSList
     * @param   Request $request
     * @return   view('GatepassSeries.gatepass-series-table');
     */
    public function gsList(Request $request)
    {
        if($request->ajax()) {

            $assigned_list = GS::where('active_status', 1)->get();
            $data = collect();

            if($assigned_list->count() > 0) {
                $ctr = 1;
                foreach($assigned_list as $al) {
                    $data->push([
                        'id' => $ctr,
                        'series' => $al->from . "-" . $al->to,
                        'location' => strtoupper($this->getValueById($al->farm_location_id, "farm")),
                        'department_division' => strtoupper($this->getValueById($al->department_division_id, "dep")),
                        'date_assigned' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' =>
                            (ACC::checkAccess(Auth::id(), 'gateser_edit') ?
                                '<a href="' . route('gatepass.series.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'gateser_del') ?
                                "| <a href='" . route('delete.item', ['type' => 'GatepassSeries', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'gateser_edit') == false && ACC::checkAccess(Auth::id(), 'gateser_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }

        $farm_location_list = FarmLocation::where('active_status', 1)->get();
        $department_division_list = DepartmentDivision::where('active_status', 1)->get();
        return view('GatepassSeries.gatepass-series-table', ['farm_location_list' => $farm_location_list, 'department_division_list' => $department_division_list]);
    }


    /**
     * GSLocationOrDepartment
     * This Function is for Filtering the table using Location and Department
     * @param   $field_name, $field_value
     * @return   DataTables::of($data)->rawColumns(['action'])->make(true);
     */
    public function gsLocationOrDepartment($field_name = null, $field_value = null)
    {
        $data = collect();

        $assigned_list = GS::where('active_status', 1)
            ->where($field_name == 'location' ? "farm_location_id" : "department_division_id", $field_value)
            ->get();

        if ($assigned_list->count() > 0) {
            foreach ($assigned_list as $key => $al) {
                $data->push([
                    'id' => $key + 1,
                    'series' => $al->from . "-" . $al->to,
                    'location' => strtoupper($this->getValueById($al->farm_location_id, "farm")),
                    'department_division' => strtoupper($this->getValueById($al->department_division_id, "dep")),
                    'date_assigned' => $al->created_at->format('d-m-Y / H:i:s'),
                    'action' =>
                        (ACC::checkAccess(Auth::id(), 'gateser_edit') ?
                            '<a href="' . route('gatepass.series.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                UPDATE
                            </a> "
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'gateser_del') ?
                            "| <a href='" . route('delete.item', ['type' => 'GatepassSeries', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                DELETE
                            </a>"
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'gateser_edit') == false && ACC::checkAccess(Auth::id(), 'gateser_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                ]);
            }
        }

        return DataTables::of($data)->rawColumns(['action'])->make(true);
    }

    public function gsRemoveFilter()
    {
        $assigned_list = GS::where('active_status', 1)->get();
        $data = collect();

        if($assigned_list->count() > 0) {
            $ctr = 1;
            foreach($assigned_list as $al) {
                $data->push([
                    'id' => $ctr,
                    'series' => $al->from . "-" . $al->to,
                    'location' => strtoupper($this->getValueById($al->farm_location_id, "farm")),
                    'department_division' => strtoupper($this->getValueById($al->department_division_id, "dep")),
                    'date_assigned' => $al->created_at->format('d-m-Y / H:i:s'),
                    'action' =>
                        (ACC::checkAccess(Auth::id(), 'gateser_edit') ?
                            '<a href="' . route('gatepass.series.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                UPDATE
                            </a> "
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'gateser_del') ?
                            "| <a href='" . route('delete.item', ['type' => 'GatepassSeries', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                DELETE
                            </a>"
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'gateser_edit') == false && ACC::checkAccess(Auth::id(), 'gateser_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                ]);
                $ctr++;
            }
        }
        return DataTables::of($data)
                ->rawColumns(['action'])
                ->make(true);
    }

    public function gsAll($from = null, $to = null, $field_location = null, $field_department = null)
    {
        $data = collect();
        $assigned_list = GS::where('active_status', 1)->get();

        if (($from == 0 && $to == 0) || (is_null($from) && is_null($to)) || ($from == '' && $to == '') || ($from == 'NaN' && $to == 'NaN'))
        {
            if ($field_location == '' || $field_location == null){
                $assigned_list = GS::where('active_status', 1)
                    ->where('department_division_id', $field_department)
                    ->get();
            }elseif (($field_location != '' || $field_location != null) && ($field_department != '' || $field_department != null)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('farm_location_id', $field_location)
                    ->where('department_division_id', $field_department)
                    ->get();
            }
        }elseif(($from != 0 && $to != 0) || (!is_null($from) && !is_null($to)) || ($from != '' && $to != ''))
        {
            if (($field_location == '' || $field_location == null) && ($field_department == '' || $field_department == null)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('from', '>=', $from)
                    ->where('to', '<=', $to)
                    ->get();
            }elseif (($field_location != '' || $field_location != null || $field_location != 0) && ($field_department == '' || $field_department == null || $field_department == 0)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('from', '>=', $from)
                    ->where('to', '<=', $to)
                    ->where('farm_location_id', $field_location)
                    ->get();
            }elseif (($field_location == '' || $field_location == null || $field_location == 0) && ($field_department != '' || $field_department != null || $field_department != 0)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('from', '>=', $from)
                    ->where('to', '<=', $to)
                    ->where('department_division_id', $field_department)
                    ->get();
            }else{
                $assigned_list = GS::where('active_status', 1)
                    ->where('from', '>=', $from)
                    ->where('to', '<=', $to)
                    ->where('farm_location_id', $field_location)
                    ->where('department_division_id', $field_department)
                    ->get();
            }
        }elseif(($from != 0 && $to == 0) || (!is_null($from) && is_null($to)) || ($from != '' && $to == ''))
        {
            if (($field_location != '' || $field_location != null || $field_location != 0) && ($field_department != '' || $field_department != null || $field_department != 0)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('from', '>=', $from)
                    ->where('farm_location_id', $field_location)
                    ->where('department_division_id', $field_department)
                    ->get();
            }elseif(($field_location == '' || $field_location == null || $field_location == 0) && ($field_department != '' || $field_department != null || $field_department != 0)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('from', '>=', $from)
                    ->where('department_division_id', $field_department)
                    ->get();
            }elseif(($field_location != '' || $field_location != null || $field_location != 0) && ($field_department == '' || $field_department == null || $field_department == 0)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('from', '>=', $from)
                    ->where('farm_location_id', $field_location)
                    ->get();
            }
        }
        elseif(($from == 0 && $to != 0) || (is_null($from) && !is_null($to)) || ($from == '' && $to != ''))
        {
            if (($field_location != '' || $field_location != null || $field_location != 0) && ($field_department != '' || $field_department != null || $field_department != 0)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('to', '<=', $to)
                    ->where('farm_location_id', $field_location)
                    ->where('department_division_id', $field_department)
                    ->get();
            }elseif(($field_location == '' || $field_location == null || $field_location == 0) && ($field_department != '' || $field_department != null || $field_department != 0)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('to', '<=', $to)
                    ->where('department_division_id', $field_department)
                    ->get();
            }elseif(($field_location != '' || $field_location != null || $field_location != 0) && ($field_department == '' || $field_department == null || $field_department == 0)){
                $assigned_list = GS::where('active_status', 1)
                    ->where('to', '<=', $to)
                    ->where('farm_location_id', $field_location)
                    ->get();
            }
        }

        $data = collect();
        foreach ($assigned_list as $key => $al) {
            $data->push([
                'id' => $key + 1,
                'series' => $al->from . "-" . $al->to,
                'location' => strtoupper($this->getValueById($al->farm_location_id, "farm")),
                'department_division' => strtoupper($this->getValueById($al->department_division_id, "dep")),
                'date_assigned' => $al->created_at->format('d-m-Y / H:i:s'),
                'action' =>
                    (ACC::checkAccess(Auth::id(), 'gateser_edit') ?
                        '<a href="' . route('gatepass.series.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                            UPDATE
                        </a> "
                        : "") .
                    (ACC::checkAccess(Auth::id(), 'gateser_del') ?
                        "| <a href='" . route('delete.item', ['type' => 'GatepassSeries', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                            DELETE
                        </a>"
                        : "") .
                    (ACC::checkAccess(Auth::id(), 'gateser_edit') == false && ACC::checkAccess(Auth::id(), 'gateser_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
            ]);
        }

        return DataTables::of($data)->rawColumns(['action', 'status'])->make(true);

    }
}
