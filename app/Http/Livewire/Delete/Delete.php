<?php

namespace App\Http\Livewire\Delete;

use Livewire\Component;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Models\User;
use App\Models\Audit;
use App\Models\Access;


use App\Models\Alert;//
use App\Models\AlertType;//
use App\Models\Approvals;//
use App\Models\Category;//
use App\Models\DepartmentDivision;//
use App\Models\FarmLocation;//
use App\Models\FrequenVisit;//
use App\Models\InventoryHistory;//
use App\Models\Item;//
use App\Models\FarmInventory;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\ItemName as ITNAME;
use App\Models\ItemList;//
use App\Models\ItemLocation;//
use App\Models\Location;//
use App\Models\RequestItem;//
use App\Models\Supplier;//
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\UnitOfMeasurement;
use App\Models\UsedSeries;//
use App\Models\WithdrawalSeries;

class Delete extends Component
{
    protected $listeners = ['deleteData'];
    public $item_type;
    public $item_id;
    public $item_name;
    public $deleted_status = false;

    public function mount($type, $id)
    {
        $this->item_type = $type;
        $this->item_id = $id;

        switch ($type) {
            case "Alert":
                $this->item_name = Alert::findorfail($id)->message;
                break;
            case "AlertType":
                $this->item_name = AlertType::findorfail($id)->name;
                break;
            case "Approvals":
                $this->item_name = Approvals::findorfail($id)->title;
                break;
            case "Category":
                $this->item_name = Category::findorfail($id)->category_name;
                break;
            case "SubCategory":
                $this->item_name = SCT::findorfail($id)->subcategory_name;
                break;
            case "Product":
                $this->item_name = PRD::findorfail($id)->product_name;
                break;
            case "ItemName":
                $this->item_name = ITNAME::findorfail($id)->item_name;
                break;
            case "DepartmentDivision":
                $this->item_name = DepartmentDivision::findorfail($id)->department_division;
                break;
            case "FarmLocation":
                $this->item_name = FarmLocation::findorfail($id)->farm_location;
                break;
            case "Item":
                $this->item_name = ITNAME::findorfail(Item::findorfail($id)->item_name_id)->item_name;
                break;
            case "FarmItem":
                $this->item_name = ITNAME::findorfail(Item::findorfail(FarmInventory::findorfail($id)->item_id)->item_name_id)->item_name;
                break;
            case "Location":
                $this->item_name = Location::findorfail($id)->location_name;
                break;
            case "RequestItem":
                $this->item_name = RequestItem::findorfail($id)->series_number;
                break;
            case "Supplier":
                $this->item_name = Supplier::findorfail($id)->supplier_name;
                break;
            case "Transaction":
                $this->item_name = TransactionType::findorfail(Transaction::findorfail($id)->transaction_type_id)->transaction_type;
                break;
            case "TransactionType":
                $this->item_name = TransactionType::findorfail($id)->transaction_type;
                break;
            case "UnitOfMeasurement":
                $this->item_name = UnitOfMeasurement::findorfail($id)->terminology;
                break;
            case "WithdrawalSeries":
                $this->item_name = WithdrawalSeries::findorfail($id)->from . " to " . WithdrawalSeries::findorfail($id)->to;
                break;
            
            default:
                abort(404); // Handle unknown type
        }
    }

    public function deleteData()
    {
        $to_be_deleted = null;

        // try{
        switch ($this->item_type) {
            case "Alert":
                $to_be_deleted = Alert::findorfail($this->item_id);
                break;
            case "AlertType":
                $to_be_deleted = AlertType::findorfail($this->item_id);
                break;
            case "Approvals":
                $to_be_deleted = Approvals::findorfail($this->item_id);
                break;
            case "Category":
                $to_be_deleted = Category::findorfail($this->item_id);
                break;
            case "SubCategory":
                $to_be_deleted = SCT::findorfail($this->item_id);
                break;
            case "Product":
                $to_be_deleted = PRD::findorfail($this->item_id);
                break;
            case "ItemName":
                $to_be_deleted = ITNAME::findorfail($this->item_id);
                break;
            case "DepartmentDivision":
                $to_be_deleted = DepartmentDivision::findorfail($this->item_id);
                break;
            case "FarmLocation":
                $to_be_deleted = FarmLocation::findorfail($this->item_id);
                break;
            case "FrequenVisit":
                $to_be_deleted = FrequenVisit::findorfail($this->item_id);
                break;
            case "InventoryHistory":
                $to_be_deleted = InventoryHistory::findorfail($this->item_id);
                break;
            case "Item":
                $to_be_deleted = Item::findorfail($this->item_id);
                break;
            case "FarmItem":
                $to_be_deleted = FarmInventory::findorfail($this->item_id);
                break;
            case "ItemList":
                $to_be_deleted = ItemList::findorfail($this->item_id);
                break;
            case "ItemLocation":
                $to_be_deleted = ItemLocation::findorfail($this->item_id);
                break;
            case "Location":
                $to_be_deleted = Location::findorfail($this->item_id);
                break;
            case "RequestItem":
                $to_be_deleted = RequestItem::findorfail($this->item_id);
                break;
            case "Supplier":
                $to_be_deleted = Supplier::findorfail($this->item_id);
                break;
            case "Transaction":
                $to_be_deleted = Transaction::findorfail($this->item_id);
                break;
            case "TransactionType":
                $to_be_deleted = TransactionType::findorfail($this->item_id);
                break;
            case "UnitOfMeasurement":
                $to_be_deleted = UnitOfMeasurement::findorfail($this->item_id);
                break;
            case "UsedSeries":
                $to_be_deleted = UsedSeries::findorfail($this->item_id);
                break;
            case "WithdrawalSeries":
                $to_be_deleted = WithdrawalSeries::findorfail($this->item_id);
                break;
            
            default:
                abort(404); // Handle unknown type
        }
        $to_be_deleted_old = $to_be_deleted;
        // $to_be_deleted->active_status = 0;

        try {
            $this->deleted_status = true;
            $to_be_deleted->delete();
            $log_entry = [
                'Delete',
                $this->item_type,
                $to_be_deleted_old,
                '',
            ];
            AC::logEntry($log_entry);
            $this->emit('deletedStatus', 1);

        } catch (QueryException $e) 
        {

            $this->deleted_status = false; 
            $log_entry = [
                'Delete Failed',
                $this->item_type,
                $to_be_deleted,
                '',
            ];
            AC::logEntry($log_entry);
            $this->emit('deletedStatus', 0);
        }
    }

    public function render()
    {
        return view('livewire.delete.delete');
    }
}
