<?php

namespace App\Http\Livewire\UnitOfMeasurement;

use Livewire\Component;

use App\Models\UnitOfMeasurement as UOM;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class UnitOfMeasurement extends Component
{

    public $terminology;
    public $abbreviation;
    public $listeners = [];
    public $via_item = null;

    protected function rules()
    {
        $all_data = UOM::where('active_status', 1)->get();
        return [
            'terminology' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->terminology) == strtolower($value)) {
                            $fail("This Unit Of Measurement Already Exist");
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
            'terminology.required' => 'The field "Unit Of Measurement:" is Required',
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
            $terminology = new UOM();

            $similarity_percentage = GC::compare_strings(strtoupper($this->terminology),'unit_of_measurements','terminology');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->terminology) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/uom');
            }

            $terminology->terminology = $this->terminology;
            $terminology->abbreviation = $this->abbreviation;
            $terminology->active_status = true;
            $terminology->deleted_status = false;
            $terminology->save();
            // create log for the data
            $log_entry = [
                'Create',
                'Unit Of Measurement',
                '',
                $terminology,
            ];
            AC::logEntry($log_entry);

            if ($this->via_item == 'item') {
                session()->flash('success_two', 'New UOM Succesfully Created!');
                return redirect('/uom');
            }
            session()->flash('success', 'New UOM Succesfully Created!');
            return redirect('/uom');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create UOM!');
            return redirect('/uom');
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
        return view('livewire.unit-of-measurement.unit-of-measurement');
    }
}
