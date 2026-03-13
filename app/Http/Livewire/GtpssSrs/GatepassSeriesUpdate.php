<?php

namespace App\Http\Livewire\GtpssSrs;

use Livewire\Component;

use App\Models\GatepassSeries as GS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;

class GatepassSeriesUpdate extends Component
{

    public $from;
    public $to;
    public $farm_location_id;
    public $department_division_id;
    public $listeners = [];
    public $farm_location_list;
    public $department_division_list;

    public $gate_series_id;

    protected function rules()
    {
        $gatepassSeries = GS::where('active_status', 1)->where('id', '!=', $this->gate_series_id)->get();

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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        // call functioneditRecord
        $this->listeners=  ['editRecord'];

        $this->gate_series_id = $id;

        $gatepass_series = GS::findorfail($id);

        $this->from = $gatepass_series->from;
        $this->to = $gatepass_series->to;
        $this->farm_location_id = $gatepass_series->farm_location_id;
        $this->department_division_id = $gatepass_series->department_division_id;

        $this->farm_location_list = FarmLocation::where('active_status', 1)->get();
        $this->department_division_list = DepartmentDivision::where('active_status', 1)->get();
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // edit record in the database
            $gatepass_series = GS::findorfail($this->with_series_id);
            $gatepass_series->from = $this->from;
            $gatepass_series->to = $this->to;
            $gatepass_series->farm_location_id = $this->farm_location_id;
            $gatepass_series->department_division_id = $this->department_division_id;
            $gatepass_series->active_status = true;
            $gatepass_series->deleted_status = false;

            $gatepass_series->save();
            // create log for the data
            $log_entry = [
                'Update',
                'Gate Pass Series',
                '',
                $gatepass_series,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Gate Pass Series Succesfully Updated!');

            // reset form fields
            $this->reset(['from', 'to', 'farm_location_id', 'department_division_id']);

            return redirect('/gatepass/series');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Gste Pass Series!');
            return redirect('/gatepass/series');
        }

    }

    public function render()
    {
        return view('livewire.gtpss-srs.gatepass-series-update');
    }
}
