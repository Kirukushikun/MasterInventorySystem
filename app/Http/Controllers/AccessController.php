<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DataTables;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;
use App\Models\User;

use App\Models\FarmLocation;
use App\Models\DepartmentDivision;
use App\Models\Access;

class AccessController extends Controller
{
    /**
     * Access the specified resource.
     *
     * @throws \Exception when something goes wrong
     * @return \Illuminate\View\View the view for accessing the resource
     */
    public function access()
    {
        return view('access.access');
    }

    /**
     * Retrieves the farm access data.
     *
     * @param Request $request The request object.
     *
     * @return mixed The farm access data.
     */
    public function farmAccess(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('access')->get();
            $data = [];
            foreach ($users as $user) {
                $name = User::findorfail($user->id)->name;
                $action = $this->getAction($user, $name, $user->id);

                $data[] = [
                    'id' => $user->id,
                    'full_name' => $name,
                    'farm_location' => FarmLocation::findorfail($user->farm_location_id)->farm_location,
                    'department_division' => DepartmentDivision::findorfail($user->department_division_id)->department_division,
                    'access' => strtoupper(Access::where('user_id', $user->id)->value('access') ?: 'NULL'),
                    'action' => 
                        (ACC::checkAccess(Auth::id(), 'access_set') ? 
                            $action : '<a class="btn btn-info disabled">N/A</a>')
                ];
            }
            return DataTables::of($data)->rawColumns(['action'])->make(true);

        }

        return view('access.access');
    }

    /**
     * Retrieves the action link for a given user and name.
     *
     * @param mixed $user The user object.
     * @param string $name The name of the user.
     * @return string The generated action link.
     */
    private function getAction($user, $name, $user_id)
    {
        $access = Access::where('user_id', $user_id)->first();
        $accessLists = strtoupper($access ? $access->access : 'NULL');

        $id = Crypt::encryptString($accessLists === 'NULL' ? $user_id : $access->id);
        $status = $accessLists === 'NULL' ? 'set' : 'edit';

        $route = route('access.set_acc', ['id' => $id, 'fullname' => $name, 'action' => $status]);
        $btnClass = $accessLists === 'NULL' ? 'primary' : 'success';

        return "<a href='{$route}' class='btn btn-{$btnClass} btn-sm'><i class='fas fa-user-shield'></i>" . strtoupper($status) . " USER ACCESS</a>";
    }

    /**
     * Checks if a user has access to a specific feature.
     *
     * @param int $id The user ID.
     * @param string $acc The access to check.
     * @return bool Returns true if the user has access, false otherwise.
     */
    public static function checkAccess($id, $acc)
    {
        $access = Access::where('user_id', $id)
                    ->whereRaw("FIND_IN_SET(?, access)", [$acc])
                    ->count();

        return $access > 0;
    }

    /**
     * Set User Access to Specific Module to Specific Farm
     * @param   $id User ID, $fullname Full Name of User, $code Farm Code [SWINE, PFC, BDL]
     * @return   return LiveWire View for Setting User Access
     */
    public function set_acc($id, $fullname, $action)
    {
        return view('access.access-' . $action, ['id' => GC::decryptString($id), 'fullname' => $fullname, 'action' => $action]);
    }

    /**
     * A function that checks if a user has access to a specific resource.
     *
     * @param int $id The user ID.
     * @param string $acc The resource to check access for.
     * @return string The result of the access check: 'no access' or 'has access'.
     */
    public function acc($id, $acc)
    {
        $access = Access::where('user_id', $id)
                    ->whereRaw("FIND_IN_SET(?, access)", [$acc])
                    ->first();

        return $access ? 'has access' : 'no access';
    }
}
