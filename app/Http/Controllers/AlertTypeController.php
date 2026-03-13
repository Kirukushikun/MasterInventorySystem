<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AlertType as AT;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class AlertTypeController extends Controller
{
    public function alertTypeDiv()
    {
        return view('alert-type.alert-type');
    }

    public function showAlertTypeDiv($id)
    {
        return view('alert-type.alert-type-update', ['alert_type_id' => GC::decryptString($id)]);
    }

    public function alertTypeList(Request $request)
    {
        if($request->ajax()) {

            $alert_type = AT::where('active_status', 1)->get();
            $data = collect();

            if($alert_type->count() > 0) {
                $ctr = 1;
                foreach($alert_type as $al) {
                    $data->push([
                        'id' => $ctr,
                        'name' => strtoupper($al->name),
                        'description' => strtoupper($al->description),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'alerttype_edit') ? 
                                '<a title="" href="' . route('alert.type.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'alerttype_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'AlertType', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (!ACC::checkAccess(Auth::id(), 'alerttype_del') && !ACC::checkAccess(Auth::id(), 'alerttype_edit') ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('alert-type.alert-type-table');
    }
}
