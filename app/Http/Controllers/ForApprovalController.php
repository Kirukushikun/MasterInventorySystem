<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForApprovalController extends Controller
{
    public function forApprovalList()
    {
        return view('for-approval.for-approval');
    }
}
