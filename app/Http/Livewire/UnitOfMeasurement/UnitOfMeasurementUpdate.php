<?php

namespace App\Http\Livewire\UnitOfMeasurement;

use Livewire\Component;

use App\Models\UnitOfMeasurement as UOM;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class UnitOfMeasurementUpdate extends Component
{
    public $terminology;
    public $abbreviation;
    public $listeners = [];

    public $uom_id;

    protected function rules()
    {
        $all_data = UOM::where('active_status', 1)->where('id', '!=', $this->uom_id)->get();
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
            'terminology.unique' => 'This Unit Of Measurement Already Exist',
            'abbreviation.required' => 'The field "Department Code:" is Required',
        ];
    }

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners=  ['editRecord'];

        $this->uom_id = $id;
        $terminology = UOM::findorfail($id);
        $this->terminology = $terminology->terminology;
        $this->abbreviation = $terminology->abbreviation;
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database
            $terminology = UOM::findorfail($this->uom_id);
            $terminology_old = $terminology;

            $similarity_percentage = GC::compare_strings(strtoupper($this->terminology),'unit_of_measurements','terminology', $terminology->id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->terminology) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/uom/show' . '/' . Crypt::encryptString($terminology->id));
            }

            $terminology->terminology = $this->terminology;
            $terminology->abbreviation = $this->abbreviation;
            $terminology->active_status = true;
            $terminology->deleted_status = false;

            $terminology->save();
            // create log for the data
            $log_entry = [
                'Update',
                'Unit Of Measurement',
                $terminology_old,
                $terminology,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'UOM Succesfully Updated!');

            // reset form fields
            $this->reset(['terminology', 'abbreviation']);

            return redirect('/uom');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update UOM!');
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
        return view('livewire.unit-of-measurement.unit-of-measurement-update');
    }
}
