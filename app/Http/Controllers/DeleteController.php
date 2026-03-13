<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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


class DeleteController extends Controller
{
    public function delete($type, $id)
    {
        return view('delete.delete', ['item_type' => $type, 'item_id' => GC::decryptString($id)]);
    }

    public function deleteSample($type, $id)
    {
        $to_be_deleted = null;

        switch ($type) {
            case "Alert":
                $to_be_deleted = Alert::findorfail($id);
                break;
            case "AlertType":
                $to_be_deleted = AlertType::findorfail($id);
                break;
            case "Approvals":
                $to_be_deleted = Approvals::findorfail($id);
                break;
            case "Category":
                $to_be_deleted = Category::findorfail($id);
                break;
            case "DepartmentDivision":
                $to_be_deleted = DepartmentDivision::findorfail($id);
                break;
            case "FarmLocation":
                $to_be_deleted = FarmLocation::findorfail($id);
                break;
            case "FrequenVisit":
                $to_be_deleted = FrequenVisit::findorfail($id);
                break;
            case "InventoryHistory":
                $to_be_deleted = InventoryHistory::findorfail($id);
                break;
            case "Item":
                $to_be_deleted = Item::findorfail($id);
                break;
            case "ItemList":
                $to_be_deleted = ItemList::findorfail($id);
                break;
            case "ItemLocation":
                $to_be_deleted = ItemLocation::findorfail($id);
                break;
            case "Location":
                $to_be_deleted = Location::findorfail($id);
                break;
            case "RequestItem":
                $to_be_deleted = RequestItem::findorfail($id);
                break;
            case "Supplier":
                $to_be_deleted = Supplier::findorfail($id);
                break;
            case "Transaction":
                $to_be_deleted = Transaction::findorfail($id);
                break;
            case "TransactionType":
                $to_be_deleted = TransactionType::findorfail($id);
                break;
            case "UnitOfMeasurement":
                $to_be_deleted = UnitOfMeasurement::findorfail($id);
                break;
            case "UsedSeries":
                $to_be_deleted = UsedSeries::findorfail($id);
                break;
            case "WithdrawalSeries":
                $to_be_deleted = WithdrawalSeries::findorfail($id);
                break;
            
            default:
                abort(404); // Handle unknown type
        }

        $to_be_deleted->active_status = 0;
        $to_be_deleted->save();
    }
}
