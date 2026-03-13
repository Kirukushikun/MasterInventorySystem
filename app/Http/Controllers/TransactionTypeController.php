<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TransactionType as TT;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class TransactionTypeController extends Controller
{
    public function transactionTypeDiv()
    {
        return view('transaction-type.transaction-type');
    }

    public function showTransactionTypeDiv($id)
    {
        return view('transaction-type.transaction-type-update', ['transaction_type_id' => GC::decryptString($id)]);
    }

    public function transactionTypeList(Request $request)
    {
        if($request->ajax()) {

            $transaction_type = TT::where('active_status', 1)->get();
            $data = collect();

            if($transaction_type->count() > 0) {
                $ctr = 1;
                foreach($transaction_type as $al) {
                    $data->push([
                        'id' => $ctr,
                        'name' => strtoupper($al->transaction_type),
                        'description' => strtoupper($al->transaction_type_description),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'transtype_edit') ? 
                                '<a href="' . route('transaction.type.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'transtype_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'TransactionType', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'transtype_edit') == false && ACC::checkAccess(Auth::id(), 'transtype_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('transaction-type.transaction-type-table');
    }
}
