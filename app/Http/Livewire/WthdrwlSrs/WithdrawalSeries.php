<?php

namespace App\Http\Livewire\WthdrwlSrs;

use Livewire\Component;

use App\Models\WithdrawalSeries as WS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;

class WithdrawalSeries extends Component
{
    /**
     * This public variable declaration are from the input in user(a model)
     * @param  from,to,farm_location,department_division,listeners
     */
    
    public $from;
    public $to;
    public $farm_location_id;
    public $department_division_id;
    public $listeners = [];
    public $farm_location_list;
    public $department_division_list;

    /**
     * Define protected function for validation rules
     * rules
     * @param   no parameter
     * @return  array of [from, to, farm_location, department_division]
     */
    protected function rules()
    {
        $withdrawalSeries = WS::where('active_status', 1)->get();

        return [
            'from' => [
                'required',
                'numeric',
                'regex:/^[0-9]+$/',
                'less_than_field:to',
                $this->withdrawalSeriesValidationRule($withdrawalSeries),
            ],
            'to' => [
                'required',
                'numeric',
                'regex:/^[0-9]+$/',
                'greater_than_field:from',
                $this->withdrawalSeriesValidationRule($withdrawalSeries),
            ],
            'farm_location_id' => 'required|integer',
            'department_division_id' => 'required|integer',
        ];
    }

    private function withdrawalSeriesValidationRule($withdrawalSeries)
    {
        return function ($attribute, $value, $fail) use ($withdrawalSeries) {
            $fromValue = $attribute === 'from' ? $value : $this->from;
            $toValue = $attribute === 'to' ? $value : $this->to;

            foreach ($withdrawalSeries as $series) {
                if (($fromValue >= $series->from && $fromValue <= $series->to) ||
                    ($toValue >= $series->from && $toValue <= $series->to) ||
                    ($fromValue <= $series->from && $toValue >= $series->to)) {
                    $fail("This Withdrawal Series Already Exists Or Overlapping With The Existing Data");
                    break;
                }
            }
        };
    }

    public function messages()
    {
        return [
            'to.greater_than_field' => 'The to field must be greater than from',
            'from.less_than_field' => 'The from field must be greater than to',
            'to.required' => 'The field "Series To:" is Required',
            'from.required' => 'The field "Series From:" is Required',
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

        $this->farm_location_list = FarmLocation::where('active_status', 1)->get();
        $this->department_division_list = DepartmentDivision::where('active_status', 1)->get();
    }

    /**
     * This function creates a new withdrawal series record in the database
     * functionCreateNewRecord
     * @param   no parameter
     * @return  void
     */
    public function createNewRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database

            $with_series = WS::where('farm_location_id', $this->farm_location_id)
                ->where('department_division_id', $this->department_division_id)->first();
                
            if ($with_series) {
                $with_series->active_status = 0;
                $with_series->save();
            }


            $withdrawal_series = WS::create([
                'from' => $this->from,
                'to' => $this->to,
                'farm_location_id' => $this->farm_location_id,
                'department_division_id' => $this->department_division_id,
                'active_status' => true,
                'deleted_status' => false,
            ]);
            // create log for the data
            $log_entry = [
                'Create',
                'Assign Withdrawal Series',
                '',
                $withdrawal_series,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Series Succesfully Assigned!');

            // reset form fields
            $this->reset(['from', 'to', 'farm_location_id', 'department_division_id']);

            return redirect('/withdrawal/series');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Assign Series!');
            return redirect('/withdrawal/series');
        }
        
    }

    /**
     * This function render the script to livewire blade
     * render
     * @param   no parameter
     * @return  livewire.wthdrwl-srs.withdrawal-series
     */
    public function render()
    {
        return view('livewire.wthdrwl-srs.withdrawal-series');
    }

}
