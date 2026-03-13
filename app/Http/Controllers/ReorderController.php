<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;

class ReorderController extends Controller
{

    public function showReorderDiv($id)
    {
        return view('reorder.reorder-update', ['reorder_id' => GC::decryptString($id)]);
    }

    public function reorderList(Request $request)
    {
        return view('reorder.reorder-table');
    }
}
