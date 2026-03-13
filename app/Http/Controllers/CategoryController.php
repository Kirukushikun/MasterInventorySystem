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

class CategoryController extends Controller
{
    public function categoryDiv()
    {
        return view('category.category');
    }

    public function showCategoryDiv($id)
    {
        return view('category.category-update', ['category_id' => GC::decryptString($id)]);
    }

    public function categoryList(Request $request)
    {
        if($request->ajax()) {

            $category = CT::where('active_status', 1)->get();
            $data = collect();

            if($category->count() > 0) {
                $ctr = 1;
                foreach($category as $al) {
                    $data->push([
                        'id' => $ctr,
                        'name' => strtoupper($al->category_name),
                        'description' => strtoupper($al->category_description),
                        'date_created' => $al->created_at->format('d-m-Y / H:i:s'),
                        'action' =>
                            (ACC::checkAccess(Auth::id(), 'cat_edit') ?
                                '<a href="' . route('category.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm'><i class='fas fa-edit'></i>
                                    UPDATE
                                </a> "
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'cat_del') ?
                                "| <a href='" . route('delete.item', ['type' => 'Category', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i>
                                    DELETE
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'cat_edit') == false && ACC::checkAccess(Auth::user()->id, 'cat_del') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                    $ctr++;
                }
            }
            return DataTables::of($data)
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('category.category-table');
    }

    // Category list
    // public function categoryListAll(){
    //     $category = CT::where('active_status', 1)->get();

    //     return

    // }
}
