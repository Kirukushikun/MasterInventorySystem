<?php

namespace App\Http\Livewire\Location;

use Livewire\Component;

use App\Models\Location as LC;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class LocationUpdate extends Component
{

    public $location_name;
    public $description;
    public $listeners = [];

    public $location_id;

    protected function rules()
    {
        $all_data = LC::where('active_status', 1)->where('id', '!=', $this->location_id)->get();
        return [
            'location_name' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->location_name) == strtolower($value)) {
                            $fail("This Bin Location Already Exist");
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
            'location_name.required' => 'The field "Bin Location:" is Required',
            'location_name.unique' => 'This Bin Location Already Exist',
        ];
    }

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners = ['editRecord'];

        $this->location_id = $id;
        $location = LC::findorfail($id);
        $this->location_name = $location->location_name;
        $this->description = $location->description;
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // edit record in the database
            $location = LC::findorfail($this->location_id);
            $location_old = $location;

            $similarity_percentage = GC::compare_strings(strtoupper($this->location_name),'locations','location_name', $location->id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->location_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect()->route('location.div.show', ['id' => Crypt::encryptString($location->id)]);
            }

            $location->location_name = $this->location_name;
            $location->description = $this->description;
            $location->active_status = true;
            $location->deleted_status = false;

            $location->save();
            // create log for the data
            $log_entry = [
                'Update',
                'Approvals',
                $location_old,
                $location,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Location Succesfully Updated!');
            return redirect('/location/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Location!');
            return redirect('/location/list');
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
        return view('livewire.location.location-update');
    }
}
