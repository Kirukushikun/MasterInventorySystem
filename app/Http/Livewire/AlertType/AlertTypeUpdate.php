<?php

namespace App\Http\Livewire\AlertType;

use Livewire\Component;

use App\Models\AlertType as AT;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class AlertTypeUpdate extends Component
{

    public $name;
    public $description;
    public $listeners = [];

    public $alert_id;

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners = ['editRecord'];

        $this->alert_id = $id;
        $alert_type = AT::findorfail($id);
        $this->name = $alert_type->name;
        $this->description = $alert_type->description;

        // $all_data = AT::where('active_status', 1)->where('id', '!=', $this->alert_id)->get();
        // dd($all_data);
    }

    protected function rules()
    {
        $all_data = AT::where('active_status', 1)->where('id', '!=', $this->alert_id)->get();
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
            'name.unique' => 'This Notification Type Already Exists',
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

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();
            $alert_type = AT::findorfail($this->alert_id);
            $alert_type_old = $alert_type;

            $similarity_percentage = GC::compare_strings(strtoupper($this->name),'alert_types','name', $alert_type->id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect()->route('alert.type.div.show', ['id' => Crypt::encryptString($alert_type->id)]);
            }
            // edit record in the database

            $alert_type->name = $this->name;
            $alert_type->description = $this->description;
            $alert_type->active_status = true;
            $alert_type->deleted_status = false;

            $alert_type->save();
            
            // create log for the data
            $log_entry = [
                'Update',
                'AlertType',
                $alert_type_old,
                $alert_type,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Notification Type Succesfully Updated!');
            return redirect('/alert/type/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Notification Type!');
            return redirect('/alert/type/list');
        }
    }

    public function render()
    {
        return view('livewire.alert-type.alert-type-update');
    }
}
