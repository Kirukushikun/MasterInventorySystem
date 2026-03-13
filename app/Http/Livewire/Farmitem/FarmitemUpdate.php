<?php

namespace App\Http\Livewire\Farmitem;

use Livewire\Component;

use App\Models\FarmInventory                as FIT;
use App\Models\RequestItem as RI;
use App\Models\ItemList as IL;
use App\Models\Item as ITM;
use App\Models\DepartmentDivision as DD;
use App\Models\FarmLocation as FL;
use App\Models\FarmItemHistory as FIH;
use App\Models\TransactionType as TT;
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

class FarmitemUpdate extends Component
{

    public
        $category,
        $subcategory,
        $product,
        $item_name,
        $uom,
        $quantity,
        $farm,
        $department,
        $reduced_quantity = 0,
        $reason,
        $temp_quantity = 0;

    public $listeners = [];
    public $items_id;

    public function rules()
    {
        return [
            // 'reduced_quantity'          => 'required|integer',
            'reason'                    => 'required|string|min:1',
        ];
    }

    public function mount($id)
    {
        $this->listeners        = ['editRecord'];
        $this->farm_items_id = $id;

        $farm_item               = FIT::findorfail($id);
        $this->category          = CT::findorfail(ITM::findorfail($farm_item->item_id)->category_id)->category_name;
        $this->subcategory       = SCT::findorfail(ITM::findorfail($farm_item->item_id)->subcategory_id)->subcategory_name;
        $this->product           = PRD::findorfail(ITM::findorfail($farm_item->item_id)->product_id)->product_name;
        $this->item_name         = ITNAME::findorfail(ITM::findorfail($farm_item->item_id)->item_name_id)->item_name;
        $this->uom               = UM::findorfail(ITM::findorfail($farm_item->item_id)->uom_id)->terminology;
        $this->quantity          = $farm_item->quantity;
        // $this->farm              = FL::findorfail(User::findorfail($farm_item->user_assigned_id)->department_division_id)->farm_location;
        // $this->department        = DD::findorfail(User::findorfail($farm_item->user_assigned_id)->farm_location_id)->department_division;

        $this->temp_quantity     = $farm_item->quantity;
        $this->reduced_quantity  = "";
    }

    public function sub_to_quantity()
    {

        if($this->reduced_quantity == 0 || $this->reduced_quantity == null || empty($this->reduced_quantity)){
            $this->quantity = $this->temp_quantity;
        }
        else{
            $quantity = $this->temp_quantity - $this->reduced_quantity;
            $this->quantity = $quantity;

            if ($this->quantity < $this->reduced_quantity) {
                $this->quantity = $this->temp_quantity;
            }
        }

    }

    public function editRecord()
    {
        // test if validator fails
        try{
            $this->validate();
            $farm_item                             = FIT::findorfail($this->farm_items_id);
            $farm_item_old                         = $farm_item;
            $farm_item->quantity_to_remove         = $this->temp_quantity - $this->quantity;
            $farm_item->remarks                    = $this->reason;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['For Approval', 'FOR APPROVAL', 'for approval'])
                ->first();

            if ($approval) {
                $farm_item->approval_id = $approval->id;
                $farm_item->save();
                $log_entry = [
                    'Update',
                    'Farm Item',
                    $farm_item_old,
                    $farm_item,
                ];

                AC::logEntry($log_entry);

                session()->flash('success', 'Farm Item Quantity is now For Approval!');
                return redirect('/farmitem/list');
            }
            else{
                session()->flash('success', 'For Approval not found in the Approvals Module.');
                return redirect('/farmitem/list');
            }

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Farm Item Quantity!');
            return redirect('/farmitem/list');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.farmitem.farmitem-update');
    }
}
