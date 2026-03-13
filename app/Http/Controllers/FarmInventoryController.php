<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;

use App\Models\User;

class FarmInventoryController extends Controller
{
    public function farmitemDiv()
    {
        return view('farmitem.farmitem');
    }

    public function showFarmItemDiv($id)
    {
        return view('farmitem.farmitem-update', ['farmitem_id' => GC::decryptString($id)]);
    }

    public function showFarmItemDetailsDiv($id)
    {
        return view('farmitem.farmitem-details', ['farmitem_detail_id' => GC::decryptString($id)]);
    }

    // public function showItemCheckoutDiv($id)
    // {
    //     return view('farmitem.farmitem-checkout', ['farmitem_checkout_id' => GC::decryptString($id)]);
    // }

    // public function showItemMultipleCheckoutDiv($ids)
    // {
    //     return view('farmitem.farmitem-multiple-checkout', ['farmitem_multiple_checkout_ids' => GC::decryptString($ids)]);
    // }

    public function farmItemList(Request $request)
    {
        return view('farmitem.farmitem-table');
    }
}
