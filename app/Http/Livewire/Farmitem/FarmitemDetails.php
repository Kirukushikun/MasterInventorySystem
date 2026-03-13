<?php

namespace App\Http\Livewire\Farmitem;

use Livewire\Component;

use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\WithdrawalSeries as WS;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;
use App\Models\ItemList as IL;
use App\Models\Item as IT;
use App\Models\FarmInventory as FIT;
use App\Models\InventoryHistory as IH;
use App\Models\UnitOfMeasurement as UM;
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

class FarmitemDetails extends Component
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
        $item = FIT::findorfail($id);

        $this->category     = CT::findorfail(IT::findorfail($item->item_id)->category_id)->category_name;
        $this->subcategory  = SCT::findorfail(IT::findorfail($item->item_id)->subcategory_id)->subcategory_name;
        $this->product      = PRD::findorfail(IT::findorfail($item->item_id)->product_id)->product_name;
        $this->item_name    = ITNAME::findorfail(IT::findorfail($item->item_id)->item_name_id)->item_name;

        $this->uom = UM::findorfail(IT::findorfail($item->item_id)->uom_id)->terminology;
        $this->quantity = $item->quantity ?? 'N/A';

        $this->qr_code = asset('farmqrcodes/' . $item->qr_code);
        $this->itm_img = asset('photos/' . IT::findorfail($item->item_id)->item_image);
    }

    public function render()
    {
        return view('livewire.farmitem.farmitem-details');
    }
}
