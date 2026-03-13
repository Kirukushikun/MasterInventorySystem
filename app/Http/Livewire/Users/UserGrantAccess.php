<?php

namespace App\Http\Livewire\Users;

use Livewire\Component;

use Auth;
use App\Models\WithdrawalSeries as WS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\User;
use App\Models\FarmLocation;
use App\Models\DepartmentDivision;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class UserGrantAccess extends Component
{
    public $user_name;
    public $user_role;
    public $user_farm_location;
    public $user_department_division;


    public $user_location;
    public $users_id;
    public $access_types;
    public $location_list;
    public $f_list;
    public $dd_list;
    protected $listeners = ['grantAccess'];

    public function rules()
    {
        return [
            'user_name' => 'required',
            'user_role' => 'required',
            'user_farm_location' => 'required',
            'user_department_division' => 'required',
        ];
    }

    public function mount($id)
    {

        $user = User::find($id);
        $this->user_name = GC::getUserFullName($id);
        $this->users_id = $id;

        $this->access_types  = [
            'user' => '1',
            'superuser'  => '2',
            'approver' => '3',
            'cenwh keeper' => '4',
            'cenwh approver' => '5'
        ];

        $this->f_list = FarmLocation::where('active_status', 1)->get();
        $this->dd_list = DepartmentDivision::where('active_status', 1)->get();

    }

    public function grantAccess()
    {
        // test if validator fails
        try{
            $this->validate();
            $user = new User();
            $user->id = $this->users_id;
            $user->name = $this->user_name;
            $user->role = $this->user_role;
            $user->farm_location_id = $this->user_farm_location;
            $user->department_division_id = $this->user_department_division;
            $user->active_status = 1;
            $user->deleted_status = 0;

            if (User::where('id', $this->users_id)->first() == null && $user->save()) {
                // [action, table, old_value, new_value]
                $log_entry = [
                    'Granted Access',
                    'Users',
                    '',
                    $user,
                ];
                AC::logEntry($log_entry);
                session()->flash('success', 'Granted Access to User!');

                return redirect('/user' . '?acc=1');
            }else{
                $user = User::findorfail($this->users_id);
                $user_old = $user;
                $user->name = $this->user_name;
                $user->role = $this->user_role;
                $user->farm_location_id = $this->user_farm_location;
                $user->department_division_id = $this->user_department_division;
                $user->active_status = 1;
                $user->deleted_status = 0;
                $user->save();

                $log_entry = [
                    'Granted Access',
                    'Users',
                    $user_old,
                    $user,
                ];
                AC::logEntry($log_entry);
                session()->flash('success', 'Granted Access to User!');

                return redirect('/user' . '?acc=1');
            }


        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Grant User Access!');
            return redirect('/user');
        }

    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.users.user-grant-access');
    }
}
