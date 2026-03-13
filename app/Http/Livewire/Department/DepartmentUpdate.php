<?php

namespace App\Http\Livewire\Department;

use Livewire\Component;

use App\Models\DepartmentDivision as DD;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
Use Crypt;

class DepartmentUpdate extends Component
{

    public $department_division;
    public $abbreviation;
    public $listeners = [];

    public $department_division_id;


    protected function rules()
    {
        $all_data = DD::where('active_status', 1)->where('id', '!=', $this->department_division_id)->get();
        return [
            'department_division' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->department_division) == strtolower($value)) {
                            $fail("This Department Division Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'abbreviation' => 'required|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'department_division.required' => 'The field "Department Division:" is Required',
            'department_division.unique' => 'This Deparment/Division Already Exist',
            'abbreviation.required' => 'The field "Department Code:" is Required',
        ];
    }

    public function mount($id)
    {
        // call functioneditRecord
        $this->listeners = ['editRecord'];

        $this->department_division_id = $id;

        $department_division = DD::findOrFail($id);
        $this->department_division = $department_division->department_division;
        $this->abbreviation = $department_division->abbreviation;
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database
            $department_division = DD::findOrFail($this->department_division_id);
            $department_division_old = $department_division;
            $similarity_percentage = GC::compare_strings(strtoupper($this->department_division), 'department_divisions', 'department_division', $this->department_division_id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->department_division) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect()->route('dept.div.show', ['id' => Crypt::encryptString($this->department_division_id)]);
            }

            $department_division->department_division = $this->department_division;
            $department_division->abbreviation = $this->abbreviation;
            $department_division->active_status = true;
            $department_division->deleted_status = false;

            $department_division->save();
            // create log for the data
            $log_entry = [
                'Update',
                'Department Division',
                $department_division_old,
                $department_division,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Department/Division Succesfully Updated!');
            return redirect('/department/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Department/Division!');
            return redirect('/department/list');
        }

    }

    /**
     * Real-time Validation
     * updated
     * @param   $propertyName
     * @return  void
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.department.department-update');
    }
}
