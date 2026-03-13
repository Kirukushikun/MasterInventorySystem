<?php

namespace App\Http\Livewire\Farmlocation;

use Livewire\Component;

use App\Models\FarmLocation as FL;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class FarmlocationUpdate extends Component
{
    /**
     * This public variable declaration are from the input in user(a model)
     * @param  farm_location,listeners
     */
    public $farm_location;
    public $abbreviation;
    public $listeners = [];

    public $farm_location_id;

    public function render()
    {
        return view('livewire.farmlocation.farmlocation-update');
    }

    protected function rules()
    {
        $all_data = FL::where('active_status', 1)->where('id', '!=', $this->farm_location_id)->get();
        return [
            'farm_location' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->farm_location) == strtolower($value)) {
                            $fail("This Farm Location Already Exist");
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
            'farm_location.required' => 'The field "Farm Location:" is Required',
            'farm_location.unique' => 'This Farm Location Already Exist',
            'abbreviation.required' => 'The field "Farm Location Code:" is Required',
        ];
    }

    public function mount($id)
    {
        // call editRecord() Function
        $this->listeners=  ['editRecord'];

        $this->farm_location_id = $id;

        $f_loc = FL::findOrFail($this->farm_location_id);
        $this->farm_location = $f_loc->farm_location;
        $this->abbreviation = $f_loc->abbreviation;
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // update record in the database
            $f_loc = FL::findOrFail($this->farm_location_id);

            $similarity_percentage = GC::compare_strings(strtoupper($this->farm_location),'farm_locations','farm_location',$this->farm_location_id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->farm_location) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect()->route('farm.location.show', ['id' => Crypt::encryptString($f_loc ->id)]);
            }

            $f_loc->farm_location = $this->farm_location;
            $f_loc->abbreviation  = $this->abbreviation;
            $f_loc->active_status = true;
            $f_loc->deleted_status = false;

            $f_loc->save();
            // create log for the data
            $log_entry = [
                'Update',
                'Farm Location',
                '',
                $f_loc,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Farm Location Succesfully Updated!');

            // reset form fields
            $this->reset(['farm_location', 'abbreviation']);

            return redirect('/farm/location');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Farm Location!');
            return redirect('/farm/location');
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
