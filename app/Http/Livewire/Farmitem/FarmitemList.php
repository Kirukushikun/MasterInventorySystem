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
        $items = collect();
        $canEdit = ACC::checkAccess(Auth::id(), 'farminventory_edit');
        $canDelete = ACC::checkAccess(Auth::id(), 'farminventory_del');
        $canViewDetails = ACC::checkAccess(Auth::id(), 'farminventory_details');
        $canCheckout = $user_role == "cenwh keeper" || $user_role == "superuser";

        if ($itemData->isNotEmpty()) {
            $inventoryItems = ITM::whereIn('id', $itemData->pluck('item_id')->filter()->unique())->get()->keyBy('id');
            $itemNames = ITNAME::whereIn('id', $inventoryItems->pluck('item_name_id')->filter()->unique())->get()->keyBy('id');
            $assignedUsers = User::with(['departmentDivision', 'farmLocation'])
                ->whereIn('id', $itemData->pluck('user_assigned_id')->filter()->unique())
                ->get()
                ->keyBy('id');
            $approvalTitles = Approvals::whereIn('id', $itemData->pluck('approval_id')->filter()->unique())
                ->pluck('title', 'id');
            $allowedApprovalTitles = ["Approved", "APPROVED", "approved", 'Denied', 'DENIED', 'denied', 'Rejected', 'REJECTED', 'rejected'];
            $ctr = 1;

            foreach ($itemData as $al) {
                $assignedUser = $assignedUsers->get($al->user_assigned_id);

                if (!$assignedUser || (!$canCheckout && $assignedUser->department_division_id != $dd_id)) {
                    continue;
                }

                $item = $inventoryItems->get($al->item_id);
                $itemName = $item ? optional($itemNames->get($item->item_name_id))->item_name : 'N/A';
                $titleToCheck = $approvalTitles->get($al->approval_id, 'approved');
                $encryptedId = Crypt::encryptString($al->id);

                $items->push([
                    'number' => $ctr,
                    'item_name' => $itemName ?: 'N/A',
                    'quantity' => $al->quantity,
                    'location' => trim((optional($assignedUser->farmLocation)->farm_location ?? 'N/A') . ' / ' . (optional($assignedUser->departmentDivision)->department_division ?? 'N/A')),
                    'can_edit' => $canEdit,
                    'can_delete' => $canDelete,
                    'can_view_details' => $canViewDetails,
                    'can_withdraw' => in_array($titleToCheck, $allowedApprovalTitles),
                    'withdraw_url' => route('farmitem.div.show', ['id' => $encryptedId]),
                    'delete_url' => route('delete.item', ['type' => 'FarmItem', 'id' => $encryptedId]),
                    'details_url' => route('farmitem.div.details', ['id' => $encryptedId]),
                ]);

                $ctr++;
            }
        }

        $this->emit('refreshDataTable');

        return view('livewire.farmitem.farmitem-list', [
            'items' => $items,
        ]);
    }

}
