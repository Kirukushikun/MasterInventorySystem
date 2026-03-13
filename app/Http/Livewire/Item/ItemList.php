<?php

namespace App\Http\Livewire\Item;

use Livewire\Component;

use App\Models\RequestItem as RI;
use App\Models\ItemList as IL;
use App\Models\Item as ITM;
use App\Models\Approvals;
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
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ItemList extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';
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

        // $this->emit('refreshDataTable'); // Emit event to refresh the DataTable
    }

    /**
     * Generate a random alert phrase by combining an alternative phrase with an alert phrase.
     */
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

    // public function render()
    // {
    //     $itemData = ITM::with(['category', 'subcategory', 'product', 'itemName', 'location', 'uom'])
    //                     ->where('active_status', 1)
    //                     ->where('approval_id', 7)
    //                     ->get();

    //     $itemsHtml = '';

    //     // Check if there are any items
    //     if ($itemData->isNotEmpty()) {
    //         $canCheckout = ACC::checkAccess(Auth::id(), 'inventory_checkout');
    //         $canEdit = ACC::checkAccess(Auth::id(), 'inventory_edit');
    //         $canDelete = ACC::checkAccess(Auth::id(), 'inventory_del');
    //         $canViewDetails = ACC::checkAccess(Auth::id(), 'inventory_details');

    //         foreach ($itemData as $al) {
    //             $quantity = (int)$al->quantity;
    //             $inOut = $canCheckout ?
    //                 '<a ' . ($quantity < 1 ? 'href="#" id="showAlert"' : 'href="' . route('item.div.checkout', ['id' => Crypt::encryptString($al->id)]) . '"') . " class='btn btn-primary btn-sm " . ($quantity < 1 ? 'pogi-sige-na' : '') . "'><i class='fas fa-edit'></i> CHECKOUT </a>" :
    //                 '<a class="btn btn-info disabled">N/A</a>';

    //             $action = $canEdit ? '<a href="' . route('item.div.show', ['id' => Crypt::encryptString($al->id)]) . '" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> UPDATE </a>' : '';
    //             $action .= $canDelete ? "<a href='" . route('delete.item', ['type' => 'Item', 'id' => Crypt::encryptString($al->id)]) . "' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i> DELETE </a>" : '';
    //             $action .= $canViewDetails ? "<a href='" . route('item.div.details', ['id' => Crypt::encryptString($al->id)]) . "' class='btn btn-warning btn-sm'><i class='fas fa-info'></i> DETAILS </a>" : '';
    //             $action = $action ?: '<a class="btn btn-info disabled">N/A</a>';

    //             $itemsHtml .= "
    //                 <tr>
    //                     <td>{$al->itemName->item_name}</td>
    //                     <td>{$al->quantity}</td>
    //                     <td>{$inOut} {$action}</td>
    //                 </tr>
    //             ";
    //         }
    //     }

    //     $this->emit('refreshDataTable');

    //     return view('livewire.item.item-list', [
    //         'itemsHtml' => $itemsHtml,
    //     ]);
    // }

    public function render()
    {
        $itemData = ITM::select('id', 'item_name_id', 'quantity')
            ->with(['itemName:id,item_name'])
            ->where('active_status', 1)
            ->where('approval_id', 7)
            ->orderBy('id', 'desc')
            ->paginate(15);

        $canCheckout = ACC::checkAccess(Auth::id(), 'inventory_checkout');
        $canEdit = ACC::checkAccess(Auth::id(), 'inventory_edit');
        $canDelete = ACC::checkAccess(Auth::id(), 'inventory_del');
        $canViewDetails = ACC::checkAccess(Auth::id(), 'inventory_details');

        return view('livewire.item.item-list', compact(
            'itemData',
            'canCheckout',
            'canEdit',
            'canDelete',
            'canViewDetails'
        ));
    }
}
