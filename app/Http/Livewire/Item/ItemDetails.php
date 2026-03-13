<?php

namespace App\Http\Livewire\Item;

use Livewire\Component;

use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\WithdrawalSeries as WS;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;
use App\Models\ItemList as IL;
use App\Models\Item as IT;
use App\Models\InventoryHistory as IH;
use App\Models\UnitOfMeasurement as UOM;
use App\Models\Approvals;
use App\Models\TransactionType as TT;
use App\Models\UsedSeries as US;
use App\Models\RequestItem as RI;
use App\Models\Category as CT;
use App\Models\Location as LC;
use App\Models\Supplier as SPL;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\ItemName as ITNAME;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ItemDetails extends Component
{
    public $category;
    public $subcategory;
    public $product;
    public $item_name;
    public $location;
    public $model_number;
    public $item_number;
    public $supplier;
    public $uom;
    public $quantity;
    public $current_quantity;
    public $reorder_threshold;
    public $purchase_date;
    public $expiry_date;
    public $remarks;
    public $qr_code;
    public $itm_img;

    public function mount($id)
    {
        $item = IT::findorfail($id);

        $this->category = CT::findorfail($item->category_id)->category_name ?? "N/A";
        $this->subcategory = SCT::findorfail($item->subcategory_id)->subcategory_name ?? "N/A";
        $this->product      = PRD::findorfail($item->product_id)->product_name ?? "N/A";
        $this->item_name = ITNAME::findorfail($item->item_name_id)->item_name ?? "N/A";

        $this->location = LC::findorfail($item->location_id)->location_name ?? "N/A";
        $this->model_number = strtoupper($item->model_number);
        $this->item_number = strtoupper($item->item_number);

        try {
            $supplier = SPL::findOrFail($item->supplier_id);
            $this->supplier = $supplier->supplier_name;
        } catch (\Exception $e) {
            $this->supplier = 'N/A'; // Set a default value in case of error
        }

        $this->uom = empty(UOM::findorfail($item->uom_id)) ? "N/A" : UOM::findorfail($item->uom_id)->terminology;
        $this->quantity = $item->quantity ?? 'N/A';
        $this->current_quantity = $item->current_quantity ?? 'N/A';
        $this->reorder_threshold = $item->reorder_threshold ?? 'N/A';
        $this->purchase_date = date("F d, Y", strtotime($item->purchase_date)) ?? 'N/A';
        $this->expiry_date = $item->expiry_date == NULL ? 'N/A' : date("F d, Y", strtotime($item->expiry_date));
        $this->remarks = $item->remarks ?? 'N/A';
        $this->created_at = $item->created_at ?? 'N/A';
        $this->updated_at = $item->updated_at ?? 'N/A';

        $this->qr_code = asset('qrcodes/' . $item->qr_code);
        $this->itm_img = asset('photos/' . $item->item_image);
    }

    public function render()
    {
        return view('livewire.item.item-details');
    }
}
