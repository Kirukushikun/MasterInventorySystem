<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class SubCategoryController extends Controller
{
    public function subcategoryDiv()
    {
        return view('subcategory.subcategory');
    }

    public function showSubCategoryDiv($id)
    {
        return view('subcategory.subcategory-update', ['subcategory_id' => GC::decryptString($id)]);
    }

    public function subcategoryList(Request $request)
    {
        if($request->ajax()) {

            $category = CT::where('active_status', 1)->get();
            $subcategory = SCT::where('active_status', 1)->get();
            $data = collect();

            if($subcategory->count() > 0) {
                $ctr = 1;
                foreach($subcategory as $al) {
                    $data->push([
                        'id' => $ctr,
                        'category' => strtoupper(CT::findorfail($al->category_id)->category_name),
                        'subcategory' => strtoupper($al->subcategory_name),
                        'description' => strtoupper($al->subcategory_description),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'subcat_edit') ? 
                                '<a href="' . route('subcategory.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'subcat_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'SubCategory', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'subcat_edit') == false && ACC::checkAccess(Auth::user()->id, 'subcat_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('subcategory.subcategory-table');
    }
}
