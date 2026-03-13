<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\Access;
use Auth;
use DB;


class GeneralController extends Controller
{

    /**
     * Selector Method use to select dashboard/farm
     */
    public function selector($code = NULL)
    {
        // Set in session if not exist, if exist, destroy then set
        session()->forget('farm');
        session(['farm' => $code]);
        // Check user if has access to specific farm

        // redirect to specific route
        if($code == 'PFC') {
            return redirect()->route('pfc.dashboard');
        }
        else if($code == 'BDL') {
            return redirect()->route('bdl.dashboard');
        }else if($code == 'SWINE') {
            return redirect()->route('swine.dashboard');
        }
        else {
            return abort(404);
        }
    }


    /**
     * Determine if two strings are similar or the same up to a certain percentage.
     * @param string $str_to_compare
     * @param string $table
     * @param string $column_to_pluck
     * @return bool
     */

    public static function compare_strings($str_to_compare, $table, $column_to_pluck, $entry_id = 0)
    {
        if ($entry_id == 0) {
            $get_all_data = DB::table($table)
                ->where('active_status', 1)
                ->pluck($column_to_pluck);
        }else{
            $get_all_data = DB::table($table)
                ->where('active_status', 1)
                ->where('id', '!=', $entry_id) // Ignore the specified entry ID
                ->pluck($column_to_pluck);
        }

        foreach ($get_all_data as $data) {
            $pluckedValue = strtoupper($data); // Access the plucked value directly

            if (empty($str_to_compare) || empty($pluckedValue)) {
                continue; // Skip empty values
            }

            $maxLength = max(strlen($str_to_compare), strlen($pluckedValue));
            $editDistance = levenshtein($str_to_compare, $pluckedValue);
            $similarity = ((1 - $editDistance / $maxLength) * 100);

            if ($similarity >= 90) {
                return $similarity;
            }
        }
        return false;
    }

	/**
	 * Decrypt String using unified encryption key
	 * @param  string $id - any valid encrypted id 
	 * @return string $id - decrypted value of parameter
	 */
    public static function decryptString($id)
    {
		try {
            return Crypt::decryptString($id);
        } catch (DecryptException $e) {
            return abort(500);
        }

    }


    /**
     * Encrypt String using unified encryption key
     * @param  string $string - string 
     * @return string $str - encrypted value of parameter
     */
    public static function encryptString($string)
    {
        return Crypt::encryptString($string);

    }


    /**
     * Check User if Exists or Has access to the system
     * @param   $id User ID
     * @return   True or False
     */
    public static function checkUserAccess($id)
    {
    	$user = User::find($id);

    	if(isset($user)) {
    		return true;
    	}
    	return false;
    }


    /**
     * Return Role of the User
     * @param Int $id User ID
     * @return   Return Role of the User
     */
    public static function checkUserRole($id)
    {
        $user = User::find($id);
        if(isset($user)) {
            return strtoupper($user->role);
        }
        return "N/A";
    }

    /**
     * Get Full Name of User from Auth
     * @param Int $id User ID
     * @return   Full Name of User
     */
    public static function getUserFullName($id)
    {
        $id = Crypt::encryptString($id);
        $url = config('app.root_domain') . config('app.user_details_slug') . $id;

        $response = file_get_contents($url);
        $data = json_decode($response);

        return $data->first_name . ' ' . $data->last_name;
    }

    /**
     * Check Module Access 
     * @param   $module Module Name
     * @return   True or False if it has a access to specific module
     */
    public static function checkModuleAccess($module, $farm)
    {
        if(Auth::user()->id == 1) {
            return true;
        }

        $access = Access::where('user_id', Auth::user()->id)
                    ->where('farm', $farm)
                    ->first();

        if(empty($access) || $access == null) {
            return false;
        }

        $str = strpos($access->access, ",".$module);

        if($str === false) {
            return false;
        }
        else {
            return true;
        }
    }
}