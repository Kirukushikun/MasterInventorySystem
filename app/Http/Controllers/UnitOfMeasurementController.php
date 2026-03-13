<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitOfMeasurement as UOM;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;


class UnitOfMeasurementController extends Controller
{
    public function uomDiv()
    {
        return view('uom.uom');
    }

    public function showUomDiv($id)
    {
        return view('uom.uom-update', ['uom_id' => GC::decryptString($id)]);
    }

    public function uomList(Request $request)
    {
        if($request->ajax()) {

            $uom = UOM::where('active_status', 1)->get();
            $data = collect();

            if($uom->count() > 0) {
                $ctr = 1;
                foreach($uom as $al) {
                    $data->push([
                        'id' => $ctr,
                        'terminology' => strtoupper($al->terminology),
                        'abbreviation' => strtoupper($al->abbreviation),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'uom_edit') ? 
                                '<a href="' . route('uom.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'uom_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'UnitOfMeasurement', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'uom_edit') == false && ACC::checkAccess(Auth::id(), 'uom_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('uom.uom-table');
    }
}
