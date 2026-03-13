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
use App\Models\User;
use App\Models\ItemName as ITNAME;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ItemHistory extends Component
{
    public $item_history_id;
    public function mount($id)
    {
        $this->item_history_id = $id;
    }

    public function render()
    {
        $itemData = IH::where('item_id', $this->item_history_id)->get();
        $items = [];

        if ($itemData->count() > 0) {
            $ctr = 1;
            foreach ($itemData as $al) {
                $items[] = [
                    'id'                 => $ctr,
                    'transaction_type'   => "" . strtoupper(TT::findorfail($al->transaction_type_id)->transaction_type),
                    'previous_quantity'  => $al->previous_quantity,
                    'new_quantity'       => $al->new_quantity,
                    'old_unit_price'     => 'PHP ' . number_format($al->old_unit_price, 2, '.', ','),
                    'new_unit_price'     => 'PHP ' . number_format($al->new_unit_price, 2, '.', ','),
                    'total_cost'         => 'PHP ' . number_format($al->new_unit_price * $al->new_quantity, 2, '.', ','),
                    'old_expiry_date'    => $al->old_expiry_date,
                    'new_expiry_date'    => $al->new_expiry_date,
                    'old_purchase_date'    => $al->old_purchase_date,
                    'new_purchase_date'    => $al->new_purchase_date,
                    'user'               => User::findorfail($al->user_id)->name,
                    'change_date'        => $al->change_date,
                ];
                $ctr++;
            }
        }

        $this->emit('refreshDataTable');

        return view('livewire.item.item-history', [
            'items' => $items,
        ]);
    }
}
