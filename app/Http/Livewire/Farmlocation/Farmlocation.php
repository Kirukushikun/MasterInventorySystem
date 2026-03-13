<?php

namespace App\Http\Livewire\Farmlocation;

use Livewire\Component;

use App\Models\FarmLocation as FL;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class Farmlocation extends Component
{

    /**
     * This public variable declaration are from the input in user(a model)
     * @param  farm_location,listeners
     */
    public $farm_location;
    public $abbreviation;
    public $listeners = [];

    /**
     * This function render the script to livewire blade
     * render
     * @param   no parameter
     * @return  livewire.farmlocation.farmlocation
     */
    public function render()
    {
        return view('livewire.farmlocation.farmlocation');
    }

    /**
     * Define protected function for validation rules
     * rules
     * @param   no parameter
     * @return  array of [from, to, farm_location, department_division]
     */
    protected function rules()
    {
        $all_data = FL::where('active_status', 1)->get();
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
            'abbreviation.required' => 'The field "Farm Location Code:" is Required',
        ];
    }

    /**
     * This function Initializes Value
     * mount
     * @param   formData
     * @return  void
     */
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
            $f_loc = new FL();

            $similarity_percentage = GC::compare_strings(strtoupper($this->farm_location),'farm_locations','farm_location');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->farm_location) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/farm/location');
            }

            $f_loc->farm_location = $this->farm_location;
            $f_loc->abbreviation = $this->abbreviation;
            $f_loc->active_status = true;
            $f_loc->deleted_status = false;
            $f_loc->save();
            // create log for the data
            $log_entry = [
                'Create',
                'Assign Withdrawal Series',
                '',
                $f_loc,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'New Farm Location Succesfully Created!');
            return redirect('/farm/location');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Farm Location!');
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
