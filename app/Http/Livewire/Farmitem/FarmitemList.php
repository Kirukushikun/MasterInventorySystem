<?php

namespace App\Http\Livewire\Farmitem;

use Livewire\Component;

use App\Models\RequestItem as RI;
use App\Models\ItemList as IL;
use App\Models\Item as ITM;
use App\Models\FarmInventory as FITM;
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

class FarmitemList extends Component
{
    public $selectedItems = [];
    public $sel_items = [];
    public $alertPhrase;

    public function toggleSelect($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$itemId]);
        } else {
            $this->selectedItems[] = $itemId;
        }

        // Update the sel_items array after checkbox selection changes
        $this->sel_items = $this->selectedItems;

        $this->emit('refreshDataTable'); // Emit event to refresh the DataTable
    }

    public function mount()
    {
        $alternativeAlertPhrases = [
            "Kindly restock immediately.",
            "Urgently refill the stock.",
            "Replenish inventory promptly.",
            "Restock without delay.",
            "Stock up as soon as possible.",
            "Please refill stock right now.",
            "We need a stock refill urgently.",
            "Swiftly restock the inventory.",
            "Immediate stock replenishment required.",
            "Please top up the inventory immediately.",
            "Please resupply stock right away.",
            "We require stock replenishment urgently.",
            "Restock at the earliest convenience.",
            "Stock replenishment needed ASAP.",
            "Stock renewal needed urgently.",
            "Please replenish inventory promptly.",
            "Swift restock needed.",
            "Replenish stock without delay.",
            "Please restock as quickly as possible.",
            "We need a stock refill ASAP."
        ];

        $alternativePhrases = [
            "Inventory Depleted and Unavailable",
            "Supplies Exhausted and Out of Stock",
            "Stock Depleted and Absent",
            "Resources Consumed and Unattainable",
            "Supplies Utilized and Missing",
            "Goods Consumed and Not in Stock",
            "Inventory Expended and Not Accessible",
            "Stock Utilized and Unavailable",
            "Items Depleted and Out of Reach",
            "Merchandise Consumed and Absent",
            "Goods Expended and Not Obtainable",
            "Resources Consumed and Out of Supply",
            "Inventory Used Up and Not Present",
            "Supplies Consumed and Unreachable",
            "Stock Expended and Unattainable",
            "Items Used and Out of Stock",
            "Merchandise Consumed and Lacking",
            "Inventory Depleted and Not on Hand",
            "Goods Consumed and Not in Supply",
            "Resources Utilized and Unobtainable"
        ];

        $this->alertPhrase = $alternativePhrases[array_rand($alternativePhrases)] . ", " . $alternativeAlertPhrases[array_rand($alternativeAlertPhrases)];
    }

    public function render()
    {
        $user = User::findorfail(Auth::id());
        $dd_id = $user->department_division_id;
        $user_role = $user->role;

        $itemData = FITM::where('active_status', 1)->get();
        $items = "";

        if ($itemData->isNotEmpty()) {
            $ctr = 1;
            $canEdit = ACC::checkAccess(Auth::id(), 'farminventory_edit');
            $canDelete = ACC::checkAccess(Auth::id(), 'farminventory_del');
            $canViewDetails = ACC::checkAccess(Auth::id(), 'farminventory_details');
            $canCheckout = $user_role == "cenwh keeper" || $user_role == "superuser";

            foreach ($itemData as $al) {
                $titleToCheck = (Approvals::where('id', $al->approval_id)->first() ?
                                Approvals::where('id', $al->approval_id)->first()->title : "approved");

                if ($canCheckout || User::findorfail($al->user_assigned_id)->department_division_id == $dd_id) {
                    $item = ITM::findorfail($al->item_id);
                    $itemName = ITNAME::findorfail($item->item_name_id)->item_name;
                    $quantity = $al->quantity;

                    $action = '';
                    if ($canEdit) {
                        $action .= '<a href="' . route('farmitem.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-success btn-sm " . (in_array($titleToCheck, ["Approved", "APPROVED", "approved", 'Denied', 'DENIED', 'denied', 'Rejected', 'REJECTED', 'rejected']) ? '' : 'disabled') . "'><i class='fas fa-edit'></i> WITHDRAW</a>";
                    }
                    if ($canDelete) {
                        $action .= "<a href='" . route('delete.item', ['type' => 'FarmItem', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i> DELETE</a>";
                    }
                    if ($canViewDetails) {
                        $action .= "<a href='" . route('farmitem.div.details', ['id' => Crypt::encryptString($al->id)]) . "' class='btn btn-warning btn-sm'><i class='fas fa-info'></i> DETAILS</a>";
                    }
                    $action = $action ?: '<a class="btn btn-info disabled">N/A</a>';
                    $user = User::with(['departmentDivision', 'farmLocation'])->findorfail($al->user_assigned_id);

                    $items .= "
                        <tr>
                            <td>{$ctr}</td>
                            <td>{$itemName}</td>
                            <td>{$quantity}</td>
                            <td>{$user->farmLocation->farm_location}" . " / {$user->departmentDivision->department_division} </td>
                            <td>{$action}</td>
                        </tr>
                    ";
                    $ctr++;
                }
            }
        }

        $this->emit('refreshDataTable');

        return view('livewire.farmitem.farmitem-list', [
            'items' => $items,
        ]);
    }

}
