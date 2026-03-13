<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GeneralController as GC;
use App\Models\Audit;
use Auth;
use DataTables;
use App\Models\User;

class AuditController extends Controller
{
    public static function logEntry($data)
    {
        // if(Auth::user()->id != (1 || 2)) {
        $log = new Audit();
        $log->action = $data[0];
        $log->table = $data[1];
        $log->old_value = $data[2];
        $log->new_value = $data[3];
        $log->user_id = Auth::user()->id;
        $log->save();
        // }
    }

    public function trail(Request $request)
    {
        if($request->ajax()) {
            
            $audits = Audit::orderBy('id')->get();
            // dd($audits);
            $data = collect();
            if($audits->count() > 0) {
                $ctr = 1;
                foreach($audits as $a) {
                    $data->push([
                        'id' => $ctr,
                        'user' => User::findorfail($a->user_id)->name,
                        'table' => strtoupper($a->table),
                        'action' => strtoupper($a->action),
                        'new_value' => $a->new_value,
                        'old_value' => $a->old_value,
                        'date_time' => $a->created_at->format('d-m-Y / H:i:s'),
                        'view' => '<button class="btn btn-success btn-sm" disabled>N/A</button>'
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['view'])
                    ->make(true);
        }
        return view('audit.audit');
    }
}
