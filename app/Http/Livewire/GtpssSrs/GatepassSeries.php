<?php

namespace App\Http\Livewire\GtpssSrs;

use Livewire\Component;

use App\Models\GatepassSeries as GS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;

class GatepassSeries extends Component
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
         $gatepassSeries = GS::where('active_status', 1)->get();

         return [
             'from' => [
                 'required',
                 'numeric',
                 'regex:/^[0-9]+$/',
                 'less_than_field:to',
                 $this->gatepassSeriesValidationRule($gatepassSeries),
             ],
             'to' => [
                 'required',
                 'numeric',
                 'regex:/^[0-9]+$/',
                 'greater_than_field:from',
                 $this->gatepassSeriesValidationRule($gatepassSeries),
             ],
             'farm_location_id' => 'required|integer',
             'department_division_id' => 'required|integer',
         ];
     }

     private function gatepassSeriesValidationRule($gatepassSeries)
     {
         return function ($attribute, $value, $fail) use ($gatepassSeries) {
             $fromValue = $attribute === 'from' ? $value : $this->from;
             $toValue = $attribute === 'to' ? $value : $this->to;

             foreach ($gatepassSeries as $series) {
                 if (($fromValue >= $series->from && $fromValue <= $series->to) ||
                     ($toValue >= $series->from && $toValue <= $series->to) ||
                     ($fromValue <= $series->from && $toValue >= $series->to)) {
                     $fail("This Gatepass Series Already Exists Or Overlapping With The Existing Data");
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

             $gate_series = GS::where('farm_location_id', $this->farm_location_id)
                 ->where('department_division_id', $this->department_division_id)->first();

             if ($gate_series) {
                 $gate_series->active_status = 0;
                 $gate_series->save();
             }


             $gatepass_series = GS::create([
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
                 'Assign Gate Pass Series',
                 '',
                 $gatepass_series,
             ];
             AC::logEntry($log_entry);

             // display success message to user
             session()->flash('success', 'Gate Pass Series Succesfully Assigned!');

             // reset form fields
             $this->reset(['from', 'to', 'farm_location_id', 'department_division_id']);

             return redirect('/gatepass/series');

         }catch (ValidationException $e) {

             // Validation failed, handle the error
             session()->flash('failed', 'Failed to Assign Gste Pass Series!');
             return redirect('/gatepass/series');
         }

     }

    public function render()
    {
        return view('livewire.gtpss-srs.gatepass-series');
    }
}
