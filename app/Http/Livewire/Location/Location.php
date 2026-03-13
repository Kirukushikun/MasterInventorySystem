<?php

namespace App\Http\Livewire\Location;

use Livewire\Component;

use App\Models\Location as LC;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class Location extends Component
{

    public $location_name;
    public $description;
    public $listeners = [];
    public $via_item = null;

    protected function rules()
    {
        $all_data = LC::where('active_status', 1)->get();
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
            $location = new LC();

            $similarity_percentage = GC::compare_strings(strtoupper($this->location_name),'locations','location_name');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->location_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/location');
            }

            $location->location_name = $this->location_name;
            $location->description = $this->description;
            $location->active_status = true;
            $location->deleted_status = false;
            $location->save();

            // create log for the data
            $log_entry = [
                'Create',
                'Location',
                '',
                $location,
            ];
            AC::logEntry($log_entry);

            if ($this->via_item == 'item') {
                session()->flash('success_two', 'New Location Succesfully Created!');
                return redirect('/location');

            }
            session()->flash('success', 'New Location Succesfully Created!');
            return redirect('/location');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Location!');
            return redirect('/location');
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
        return view('livewire.location.location');
    }
}
