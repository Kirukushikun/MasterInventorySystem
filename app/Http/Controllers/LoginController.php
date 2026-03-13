<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Models\FrequenVisit;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::check()) {

            return redirect()->route('dash');
        }
        $req_uri = $request->segment(1) . '/' . $request->segment(2);
        // dd($req_uri);

        if($req_uri == 'item/details'){
            return redirect()->route('item.div.details');
        }

        return view('login');
    }


    public function logout()
    {
        if(Auth::check()) {
	    	Auth::logout();
        }
    	return view('logout');
    }
}
