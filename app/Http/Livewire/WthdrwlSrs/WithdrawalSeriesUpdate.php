<?php

namespace App\Http\Livewire\WthdrwlSrs;

use Livewire\Component;

use App\Models\WithdrawalSeries as WS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;

class WithdrawalSeriesUpdate extends Component
{

    public $from;
    public $to;
    public $farm_location_id;
    public $department_division_id;
    public $listeners = [];
    public $farm_location_list;
    public $department_division_list;

    public $with_series_id;

    protected function rules()
    {
        $withdrawalSeries = WS::where('active_status', 1)->where('id', '!=', $this->with_series_id)->get();

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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        // call functioneditRecord
        $this->listeners=  ['editRecord'];

        $this->with_series_id = $id;

        $withdrawal_series = WS::findorfail($id);

        $this->from = $withdrawal_series->from;
        $this->to = $withdrawal_series->to;
        $this->farm_location_id = $withdrawal_series->farm_location_id;
        $this->department_division_id = $withdrawal_series->department_division_id;

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
            $withdrawal_series = WS::findorfail($this->with_series_id);
            $withdrawal_series->from = $this->from;
            $withdrawal_series->to = $this->to;
            $withdrawal_series->farm_location_id = $this->farm_location_id;
            $withdrawal_series->department_division_id = $this->department_division_id;
            $withdrawal_series->active_status = true;
            $withdrawal_series->deleted_status = false;

            $withdrawal_series->save();
            // create log for the data
            $log_entry = [
                'Update',
                'Withdrawal Series',
                '',
                $withdrawal_series,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Series Succesfully Updated!');

            // reset form fields
            $this->reset(['from', 'to', 'farm_location_id', 'department_division_id']);

            return redirect('/withdrawal/series');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Series!');
            return redirect('/withdrawal/series');
        }

    }

    public function render()
    {
        return view('livewire.wthdrwl-srs.withdrawal-series-update');
    }
}
