<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DepartmentDivision as DD;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class DepartmentDivisionController extends Controller
{
    /**
     * Department Division
     */
    public function deptDiv()
    {
        return view('department.department');
    }

    public function showDeptDiv($id)
    {
        return view('department.department-update', ['department_division_id' => GC::decryptString($id)]);
    }

    /**
     * flList
     * @param   Request $request
     * @return   view('department.department-table');
     */
    public function ddList(Request $request)
    {
        if($request->ajax()) {

            $department = DD::where('active_status', 1)->get();
            $data = collect();

            if($department->count() > 0) {
                $ctr = 1;
                foreach($department as $al) {
                    $data->push([
                        'id' => $ctr,
                        'department' => strtoupper($al->department_division),
                        'abbreviation' => strtoupper($al->abbreviation),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'deptdiv_edit') ? 
                                '<a href="' . route('dept.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'deptdiv_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'DepartmentDivision', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'deptdiv_edit') == false && ACC::checkAccess(Auth::user()->id, 'deptdiv_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('department.department-table');
    }

    public function ddRemoveFilter()
    {
        $department_division = DD::where('active_status', 1)->get();
        $data = collect();

        if($department_division->count() > 0) {
            $ctr = 1;
            foreach($department_division as $al) {
                $data->push([
                    'id' => $ctr,
                    'department' => strtoupper($al->department_division),
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
    public function ddDepartment($field_name = null, $field_value = null)
    {
        $data = collect();

        $department_division = DD::where('active_status', 1)
            ->where($field_name == 'location' ? "department_division" : "department_division", $field_value)
            ->get();

        if ($department_division->count() > 0) {
            foreach ($department_division as $key => $al) {
                $data->push([
                    'id' => $key + 1,
                    'location' => strtoupper($al->department_division),
                    'abbreviation' => strtoupper($al->abbreviation),
                    'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                    'action' => '<button class="btn btn-success btn-sm" disabled>N/A</button>'
                ]);
            }
        }

        return DataTables::of($data)->rawColumns(['action'])->make(true);
    }

    public function ddAll($field_location = null)
    {

        $department_division = DD::where('active_status', 1)
            ->where('department_division', $field_location)
            ->get();

        $data = collect();
        foreach ($department_division as $key => $al) {
            $data->push([
                'id' => $key + 1,
                'location' => strtoupper($al->department_division),
                'abbreviation' => strtoupper($al->abbreviation),
                'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                'action' => '<button class="btn btn-success btn-sm" disabled>N/A</button>'
            ]);
        }

        return DataTables::of($data)->rawColumns(['action', 'status'])->make(true);

    }
}
