<?php

namespace App\Http\Livewire\Notif;

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

class Notif extends Component
{
    public $notif_items = [], $ctr;

    public function mount()
    {
        $itemData = ITM::where('active_status', 0)->where('approval_id', 7)->with(['category', 'subcategory', 'product', 'itemName', 'location'])->get();
        //

        if ($itemData->isNotEmpty()) {
            $this->notif_items = [];
            foreach ($itemData as $al) {
                $excess = (int)$al->quantity - (int)$al->reorder_threshold;
                $perc = 0;

                if ($al->reorder_threshold > 0) {
                    $perc = round($excess / (int)$al->reorder_threshold * 100, 0);
                }

                $badges = 'badge-warning';
                $progressColorClass = 'bg-warning';
                $stats = '<span class="badge ' . $badges . '">WARNING</span>';

                if ($al->reorder_threshold == 0) {
                    $badges = 'badge-dark';
                    $stats = '<span class="badge badge-dark">RE-ORDER NOT SET</span>';
                }

                if (($perc >= 0 && $perc <= 9) || $al->reorder_threshold == 0) {
                    $this->notif_items[] = [
                        're_order_id' => $al->id,
                        'category' => $al->category->category_name,
                        'subcategory' => $al->subcategory->subcategory_name,
                        'product' => $al->product->product_name,
                        'item_name' => $al->itemName->item_name,
                        'location' => $al->location->location_name,
                        'reorder' => $al->reorder_threshold,
                        'quantity_on_hand' => $al->quantity,
                        'badge' => $badges,
                        'bg' => $progressColorClass,
                        'stats' => $stats,
                        'perc' => $perc,
                    ];
                }
            }
            $this->ctr = count($this->notif_items);
        }
    }

    public function render()
    {
        return view('livewire.notif.notif');
    }
}
