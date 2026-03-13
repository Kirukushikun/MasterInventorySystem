<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DataTables;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;


class UsersController extends Controller
{
    /**
     * Users
     */
    public function users()
    {
        return view('user.users');
    }

    /**
     * Users Grant Access
     */
    public function usersGrantAccess($id)
    {
        return view('user.users-grant-access', ['user_id' => GC::decryptString($id)]);
    }

    /**
     * [userJsonToDataTables Return DataTables Format JSON]
     * @param  Request $request HTTP Request
     * @return JSON DataTables Format
     */
    public function userJsonToDataTables(Request $request)
    {
        if($request->ajax()) {
            $url = config('app.root_domain') . config('app.users_api_slug');

            $response = file_get_contents($url);
            $data = json_decode($response);
            $collect = collect();
            if(count($data) > 0) {
                foreach($data as $d) {
                    $id = GC::decryptString($d->id);
                    $collect->push([
                        'id' => $id,
                        'first_name' => $d->first_name,
                        'last_name' => $d->last_name,
                        'system_access' => GC::checkUserAccess($id) ? '<span class="badge bg-success">Granted</span>' : '<span class="badge bg-danger">No Access</span>',
                        'role' => GC::checkUserRole($id),
                        'action' =>
                            (ACC::checkAccess(Auth::id(), 'users_grant') ? 
                                '<a href="' . route('user.grant.access', ['id' => Crypt::encryptString($id)]) . '"' .  " class='btn btn-primary btn-sm'><i class='fas fa-check'></i>
                                    GRANT ACCESS
                                </a>"
                                 : "") .
                            (ACC::checkAccess(Auth::id(), 'users_grant') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                    ]);
                }
            }
            return DataTables::of($collect)
                    ->rawColumns(['system_access', 'action'])
                    ->make(true);
        }
        return view('user.users');
    }

    /**
     * Grant Access with Role in the System
     * @param   $id User ID from Auth, $role role type
     * @return  True or False Value
     */
    public function grantAccess($id = null, $role = null)
    {
        $user = User::find($id);
        $name = GC::getUserFullName($id);
        $old_value = $user;
        if(isset($user)) {
            $user->name = $name;
            $user->role = $role;
            if($user->save()) {
                // [action, table, old_value, new_value]
                $log_entry = [
                    'Granted Access',
                    'users',
                    $old_value,
                    $user,
                ];
                AC::logEntry($log_entry);
            }
        }
        else {
            $user = new User();
            $user->id = $id;
            $user->name = $name;
            $user->role = $role;
            if($user->save()) {
                // [action, table, old_value, new_value]
                $log_entry = [
                    'Granted Access',
                    'users',
                    '',
                    $user,
                ];
                AC::logEntry($log_entry);
                return redirect('/user')->with('success_message', 'Access Granted!');
            }
            return redirect('/user')->with('success_message', 'Access Granted!');
        }

    }
}