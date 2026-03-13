<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;

class ProductController extends Controller
{
    public function productDiv()
    {
        return view('product.product');
    }

    public function showProductDiv($id)
    {
        return view('product.product-update', ['product_id' => GC::decryptString($id)]);
    }

    public function productList(Request $request)
    {
        if($request->ajax()) {

            $category = CT::where('active_status', 1)->get();
            $subcategory = SCT::where('active_status', 1)->get();
            $product = PRD::where('active_status', 1)->get();
            $data = collect();

            if($product->count() > 0) {
                $ctr = 1;
                foreach($product as $al) {
                    $data->push([
                        'id' => $ctr,
                        'category' => strtoupper(CT::findorfail($al->category_id)->category_name),
                        'subcategory' => strtoupper(SCT::findorfail($al->subcategory_id)->subcategory_name),
                        'name' => strtoupper($al->product_name),
                        'description' => strtoupper($al->product_description),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' => 
                            (ACC::checkAccess(Auth::id(), 'product_edit') ? 
                                '<a href="' . route('product.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'product_del') ? 
                                "| <a href='" . route('delete.item', ['type' => 'Product', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'product_edit') == false && ACC::checkAccess(Auth::user()->id, 'product_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('product.product-table');
    }
}
