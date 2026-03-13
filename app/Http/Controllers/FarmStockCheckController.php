<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\GeneralController as GC;

class FarmStockCheckController extends Controller
{
    public function showCheckFarmStock($id)
    {
        return view('farm-stock-check.farm-stock-check', ['farm_stock_check_id' => unserialize(GC::decryptString($id))]);
    }
}
