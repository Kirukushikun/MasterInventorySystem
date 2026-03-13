<?php

namespace App\Http\Livewire\AlertType;

use Livewire\Component;

use Illuminate\Http\Request;
use App\Models\AlertType as AT;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class AlertType extends Component
{
    public $name;
    public $description;
    public $listeners = [];

    protected function rules()
    {
        $all_data = AT::where('active_status', 1)->get();
        return [
            'name' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->name) == strtolower($value)) {
                            $fail("This Notification Type Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'description' => 'nullable|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The field "Notification Type:" is Required',
        ];
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
            $alert_type = new AT();

            $similarity_percentage = GC::compare_strings(strtoupper($this->name),'alert_types','name');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/alert/type');
            }
            // create new record in the database
            $alert_type->name = $this->name;
            $alert_type->description = $this->description;
            $alert_type->active_status = true;
            $alert_type->deleted_status = false;
            $alert_type->save();
            // create log for the data
            $log_entry = [
                'Create',
                'AlertType',
                '',
                $alert_type,
            ];

            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'New Notification Type Succesfully Created!');
            return redirect('/alert/type');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Notification Type!');
            return redirect('/alert/type');
        }
    }

    public function render()
    {
        return view('livewire.alert-type.alert-type');
    }
}
