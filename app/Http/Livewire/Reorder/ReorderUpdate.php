<?php

namespace App\Http\Livewire\Reorder;

use Livewire\Component;

use App\Models\FarmInventory                as FIT;
use App\Models\RequestItem as RI;
use App\Models\ItemList as IL;
use App\Models\Item as ITM;
use App\Models\DepartmentDivision as DD;
use App\Models\FarmLocation as FL;
use App\Models\Approvals;
use App\Models\User;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;
use App\Http\Controllers\WithdrawalSeriesController as WSC;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\ItemName as ITNAME;
use App\Models\Location as LC;
use App\Models\UnitOfMeasurement as UM;

class ReorderUpdate extends Component
{

    public
        $category,
        $subcategory,
        $product,
        $item_name,
        $bin_location,
        $reorder,
        $temp_reorder,
        $average_usage,
        $average_lead_time,
        $time_unit = "DAY(S)",
        // $safety_stock,
        $total_quantity,
        $consumed,
        $quantity_on_hand;

    public $listeners = [];
    public $items_id;

    public function rules()
    {
        return [
            'reorder'            => 'required|integer|min:0',
            'average_usage'      => 'required|integer|min:0',
            'average_lead_time'  => 'required|integer|min:0',
            'time_unit'          => 'required|string',
        ];
    }

    public function mount($id)
    {
        $this->listeners        = ['editRecord'];
        $this->items_id = $id;

        $item = ITM::findorfail($id);
        $this->category           = CT::findorfail($item->category_id)->category_name;
        $this->subcategory        = SCT::findorfail($item->subcategory_id)->subcategory_name;
        $this->product            = PRD::findorfail($item->product_id)->product_name;
        $this->item_name          = ITNAME::findorfail($item->item_name_id)->item_name;
        $this->bin_location       = LC::findorfail($item->location_id)->location_name;
        $this->quantity_on_hand   = $item->quantity;
        $this->reorder            = $item->reorder_threshold;
        $this->temp_reorder       = $item->reorder_threshold;
        $this->average_usage      = $item->average_consumption;
        $this->average_lead_time  = $item->average_lead_time;
        $this->time_unit          = "DAY(S)";
        // $this->safety_stock       = $item->safety_stock;
    }

    public function editRecord()
    {
        // test if validator fails
        try{
            $this->validate();
            $item                             = ITM::findorfail($this->items_id);
            $item_old                         = $item;
            $item->reorder_threshold          = $this->reorder;
            $item->average_consumption        = $this->average_usage;
            $item->average_lead_time          = $this->average_lead_time;
            $item->time_unit                  = $this->time_unit;
            $item->save();


            $log_entry = [
                'Set/Update',
                'Item Reorder',
                $item_old,
                $item,
            ];
            AC::logEntry($log_entry);

            session()->flash('success', 'Item Re-order has been Set!');
            return redirect('/item/reorder/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Item Re-order!');
            return redirect('/item/reorder/list');
        }
    }

    // Function that will calculate the re-order threshold.
    public function calculateReorderThreshold()
    {
        $average_usage = 0;
        $average_lead_time = 0;

        if($this->average_usage == null || empty($this->average_usage) || is_null($this->average_usage) || $this->average_usage == ""){
            $average_usage = 0;
        }else{
            $average_usage = $this->average_usage;
        }

        if($this->average_lead_time == null || empty($this->average_lead_time) || is_null($this->average_lead_time) || $this->average_lead_time == ""){
            $average_lead_time = 0;
        }else{
            $average_lead_time = $this->average_lead_time;
        }

        if($this->average_lead_time == 0 || $this->average_usage == 0){
            $this->reorder = $this->temp_reorder;
        }else{
            $this->reorder = $average_usage * $average_lead_time;
        }

    }


    /**
     * A description of the updated method.
     *
     * @param datatype $propertyName description of the parameter
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.reorder.reorder-update');
    }
}
