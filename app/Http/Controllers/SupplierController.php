<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Supplier as SPL;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class SupplierController extends Controller
{
    public function supplierDiv()
    {
        return view('supplier.supplier');
    }

    public function showSupplierDiv($id)
    {
        return view('supplier.supplier-update', ['supplier_id' => GC::decryptString($id)]);
    }

    public function supplierList(Request $request)
    {
        if($request->ajax()) {

            $supplier = SPL::where('active_status', 1)->get();
            $data = collect();

            if($supplier->count() > 0) {
                $ctr = 1;
                foreach($supplier as $al) {
                    $data->push([
                        'id' => $ctr,
                        'name' => strtoupper($al->supplier_name),
                        'contact_person' => strtoupper($al->contact_person),
                        'email' => $al->contact_email,
                        'tel' => $al->contact_tel_no == '' ? "N/A" : strtoupper($al->contact_tel_no),
                        'phone_number' => strtoupper($al->contact_phone),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'supplier_edit') ? 
                                '<a href="' . route('supplier.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'supplier_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'Supplier', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'supplier_edit') == false && ACC::checkAccess(Auth::id(), 'supplier_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('supplier.supplier-table');
    }
}
