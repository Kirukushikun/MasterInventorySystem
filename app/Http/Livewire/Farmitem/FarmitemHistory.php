<?php

namespace App\Http\Livewire\Farmitem;

use Livewire\Component;

use App\Models\FarmInventory                as FIT;
use App\Models\RequestItem as RI;
use App\Models\ItemList as IL;
use App\Models\Item as ITM;
use App\Models\DepartmentDivision as DD;
use App\Models\FarmLocation as FL;
use App\Models\FarmItemHistory as FIH;
use App\Models\TransactionType as TT;
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

class FarmitemHistory extends Component
{
    public $farm_item_history_id;
    public function mount($id)
    {
        $this->farm_item_history_id = $id;
    }

    public function render()
    {
        $itemData = FIH::where('farm_item_id', $this->farm_item_history_id)->get();
        $items = [];

        if ($itemData->count() > 0) {
            $ctr = 1;
            foreach ($itemData as $al) {
                $items[] = [
                    'id'                   => $ctr,
                    'transaction_type'     => "" . strtoupper(TT::findorfail($al->transaction_type_id)->transaction_type),
                    'previous_quantity'    => $al->previous_quantity,
                    'new_quantity'         => $al->new_quantity,
                    'old_expiry_date'      => $al->old_expiry_date ?? "N/A",
                    'new_expiry_date'      => $al->new_expiry_date ?? "N/A",
                    'old_purchase_date'    => $al->old_purchase_date ?? "N/A",
                    'new_purchase_date'    => $al->new_purchase_date ?? "N/A",
                    'reason'               => $al->change_reason,
                    'user'                 => User::findorfail($al->user_id)->name,
                    'change_date'          => $al->change_date,
                ];
                $ctr++;
            }
        }

        $this->emit('refreshDataTable');

        return view('livewire.farmitem.farmitem-history', [
            'items' => $items,
        ]);
    }
}
