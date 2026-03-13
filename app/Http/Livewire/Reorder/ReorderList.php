<?php

namespace App\Http\Livewire\Reorder;

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

class ReorderList extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';
    // public function render()
    // {
    //     $itemData = ITM::where('active_status', 1)->where('approval_id', 7)->get();
    //     $items = [];

    //     if ($itemData->count() > 0) {
    //         $ctr = 1;
    //         foreach ($itemData as $al) {
    //             $badges = '';
    //             $stats = '';
    //             $progressColorClass = '';
    //             $excess = (int) $al->quantity - (int) $al->reorder_threshold;
    //             $perc = 0;

    //             try {
    //                 if ($al->reorder_threshold > 0) {
    //                     $perc = round($excess / (int) $al->reorder_threshold * 100, 0);
    //                 } else {
    //                     $perc = 0; // Avoid division by zero
    //                 }
    //             } catch (\DivisionByZeroException $e) {
    //                 $perc = 0; // Handle division by zero gracefully
    //             }

    //             if ($perc >= 50) {
    //                 $perc = 100;
    //                 $badges = 'badge-success';
    //                 $stats = '<span class="badge ' . $badges . '">ABUNDANT</span>';
    //                 $progressColorClass = 'bg-success';
    //             } elseif ($perc >= 10) {
    //                 $badges = 'badge-primary';
    //                 $stats = '<span class="badge ' . $badges . '">SUFFICIENT</span>';
    //                 $progressColorClass = 'bg-primary';
    //             } elseif ($perc >= 0) {
    //                 $badges = 'badge-warning';
    //                 $stats = '<span class="badge ' . $badges . '">WARNING</span>';
    //                 $progressColorClass = 'bg-warning';
    //             } elseif ($perc >= -99) {
    //                 $badges = 'badge-danger';
    //                 $stats = '<span class="badge ' . $badges . '">CRITICAL</span>';
    //                 $progressColorClass = 'bg-danger';
    //             } else {
    //                 $badges = 'badge-secondary';
    //                 $stats = '<span class="badge ' . $badges . '">OUT-OF-STOCK</span>';
    //                 $progressColorClass = 'bg-secondary';
    //             }

    //             if($al->reorder_threshold == 0){
    //                 $badges = 'badge-dark';
    //                 $stats = '<span class="badge badge-dark">RE-ORDER NOT SET</span>';
    //             }

    //             $items[] = [
    //                 'id'                => $ctr,
    //                 'item_name'         => ITNAME::findorfail($al->item_name_id)->item_name,
    //                 'item_image'        => '
    //                     <a>
    //                         <img src="' . asset('photos/' . $al->item_image) . '" alt="Click to zoom" class="img-thumbnail imageZoomButton" data-imageurl="' . asset('photos/' . $al->item_image) . '" style="width: 80px; height: 80px; max-width: 80px; max-height: 80px;">
    //                     </a>
    //                 ',
    //                 'category'          => CT::findorfail($al->category_id)->category_name,
    //                 'subcategory'       => SCT::findorfail($al->subcategory_id)->subcategory_name,
    //                 'product'           => PRD::findorfail($al->product_id)->product_name,
    //                 'location'          => LC::findorfail($al->location_id)->location_name,
    //                 'reorder'           => $al->reorder_threshold,
    //                 'quantity_on_hand'  => $al->quantity,
    //                 'badge'             => $badges,
    //                 'bg'                => $progressColorClass,
    //                 'stats'             => $stats,
    //                 'excess'            => $excess,
    //                 'perc'              => $perc,
    //                 'action' =>
    //                     (ACC::checkAccess(Auth::id(), 'reorder_edit') ?
    //                         '<a href="' . route('reorder.div.show', ['id' => Crypt::encryptString($al->id)]) . '"' .  " class='btn btn-primary btn-sm'><i class='fas fa-edit'></i>
    //                             Set Re-Order
    //                         </a> "
    //                          : '<a class="btn btn-info disabled">N/A</a>')
    //             ];
    //             $ctr++;
    //         }
    //     }

    //     return view('livewire.reorder.reorder-list', [
    //         'items' => $items,
    //     ]);
    // }

    public function render()
    {
        $items = ITM::select('id', 'item_name_id', 'category_id', 'subcategory_id', 'product_id', 'location_id', 'item_image', 'reorder_threshold', 'quantity')
            ->with([
                'itemName:id,item_name',
                'category:id,category_name',
                'subcategory:id,subcategory_name',
                'product:id,product_name',
                'location:id,location_name'
            ])
            ->where('active_status', 1)
            ->where('approval_id', 7)
            ->orderBy('id', 'asc')
            ->paginate(10);
            $items = $items->through(function ($al) {

            $excess = (int)$al->quantity - (int)$al->reorder_threshold;

            $perc = $al->reorder_threshold > 0
                ? round(($excess / $al->reorder_threshold) * 100)
                : 0;

            if ($al->reorder_threshold == 0) {
                $badge = 'badge-dark';
                $status = 'RE-ORDER NOT SET';
                $bg = 'bg-dark';
                $perc = 0;
            } elseif ($perc >= 50) {
                $badge = 'badge-success';
                $status = 'ABUNDANT';
                $bg = 'bg-success';
                $perc = 100;
            } elseif ($perc >= 10) {
                $badge = 'badge-primary';
                $status = 'SUFFICIENT';
                $bg = 'bg-primary';
            } elseif ($perc >= 0) {
                $badge = 'badge-warning';
                $status = 'WARNING';
                $bg = 'bg-warning';
            } else {
                $badge = 'badge-danger';
                $status = 'CRITICAL';
                $bg = 'bg-danger';
            }

            return [
                'model' => $al,
                'badge' => $badge,
                'status' => $status,
                'bg' => $bg,
                'perc' => max(0, min($perc, 100)),
                'excess' => $excess
            ];
        });

        return view('livewire.reorder.reorder-list', compact('items'));
    }
}
