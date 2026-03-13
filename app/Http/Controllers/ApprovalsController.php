<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Approvals as AP;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class ApprovalsController extends Controller
{
    public function approvalsDiv()
    {
        return view('approvals.approvals');
    }

    public function showApprovalsDiv($id)
    {
        return view('approvals.approvals-update', ['approvals_id' => GC::decryptString($id)]);
    }

    public function approvalsList(Request $request)
    {
        if($request->ajax()) {

            $approvals = AP::where('active_status', 1)->get();
            $data = collect();

            if($approvals->count() > 0) {
                $ctr = 1;
                foreach($approvals as $al) {
                    $data->push([
                        'id' => $ctr,
                        'title' => strtoupper($al->title),
                        'description' => strtoupper($al->description),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'approvals_edit') ? 
                                '<a href="' . route('approvals.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'approvals_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'Approvals', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'approvals_edit') == false && ACC::checkAccess(Auth::user()->id, 'approvals_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('approvals.approvals-table');
    }
}
