<?php

namespace App\Http\Livewire\Department;

use Livewire\Component;

use App\Models\DepartmentDivision as DD;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class Department extends Component
{

    /**
     * This public variable declaration are from the input in user(a model)
     * @param  department_division,listeners
     */
    public $department_division;
    public $abbreviation;
    public $listeners = [];

    public function render()
    {
        return view('livewire.department.department');
    }

    protected function rules()
    {
        $all_data = DD::where('active_status', 1)->get();
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
            'abbreviation.required' => 'The field "Department Code:" is Required',
        ];
    }

    public function mount($formData = null)
    {
        // call functionCreateNewRecord
        $this->listeners=  ['createNewRecord'];
    }

    public function createNewRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database
            $department_division = new DD();

            $similarity_percentage = GC::compare_strings(strtoupper($this->department_division),'department_divisions','department_division');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->department_division) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/department');
            }

            $department_division->department_division = $this->department_division;
            $department_division->abbreviation = $this->abbreviation;
            $department_division->active_status = true;
            $department_division->deleted_status = false;
            $department_division->save();
            // create log for the data
            $log_entry = [
                'Create',
                'Department Division',
                '',
                $department_division,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'New Department\Division Succesfully Created!');
            return redirect('/department');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Department Division!');
            return redirect('/department');
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
}
