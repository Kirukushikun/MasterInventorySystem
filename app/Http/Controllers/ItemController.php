<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;

use App\Models\User;

class ItemController extends Controller
{
    public function itemDiv()
    {
        return view('item.item');
    }

    public function showItemDiv($id)
    {
        return view('item.item-update', ['item_id' => GC::decryptString($id)]);
    }

    public function showItemDetailsDiv($id)
    {
        return view('item.item-details', ['item_detail_id' => GC::decryptString($id)]);
    }

    public function showItemCheckoutDiv($id)
    {
        return view('item.item-checkout', ['item_checkout_id' => GC::decryptString($id)]);
    }

    public function showItemMultipleCheckoutDiv($ids)
    {
        return view('item.item-multiple-checkout', ['item_multiple_checkout_ids' => GC::decryptString($ids)]);
    }

    public function itemList(Request $request)
    {
        return view('item.item-table');
    }
}
