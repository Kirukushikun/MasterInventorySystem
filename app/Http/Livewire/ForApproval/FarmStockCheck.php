<?php

namespace App\Http\Livewire\ForApproval;


use Livewire\Component;
use App\Models\RequestItem as RI;
use App\Models\ItemList as IL;
use App\Models\Item as ITM;
use App\Models\Approvals;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;
use App\Http\Controllers\WithdrawalSeriesController as WSC;
use App\Models\FarmItemHistory as FIH;
use App\Models\FarmInventory as FIT;
use App\Models\TransactionType as TT;
use App\Models\Transaction;
use App\Models\ItemName as ITNAME;
use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\User;
use App\Models\InventoryHistory as IH;
use App\Models\Location as LC;
use App\Models\FarmLocation as FL;
use App\Models\DepartmentDivision as DD;
use App\Models\UnitOfMeasurement as UOM;
use Illuminate\Validation\ValidationException;

class FarmStockCheck extends Component
{

    public $request_id, $filtered_farm;
    public $available_farm, $available_farm_with_requested_items;
    public $listeners = ['proceed'];
    public $available_farm_items = [
        [
            'item_id' => null,
            'item_name' => null,
            'quantity_on_stock' => null,
            'unit_of_measurement' => null,
            'requested_quantity' => null,

        ]
    ];
    public $farms;
    public $farmID = 0;
    public $chk_fraction = false, $notes = '';
    public $user;


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'available_farm' => 'required',
            'available_farm_items.*.quantity_to_release' => 'required|numeric|min:1',
        ];
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'available_farm.required' => 'Please choose a farm.',
            'available_farm_items.*.quantity_to_release.required' => 'Release quantity is required.',
            'available_farm_items.*.quantity_to_release.numeric' => 'Release quantity must be a number.',
            'available_farm_items.*.quantity_to_release.min' => 'Release quantity must be at least 1.',
        ];
    }

    /**
     * Mount the component.
     *
     * @param int $id The id of the request item.
     * @return void
     */
    public function mount($id)
    {
        $this->request_id = $id;

        $this->filtered_farm = $this->filterFarms($this->request_id);

        $this->farms = $this->getFarmsWithLocations($this->filtered_farm['user_ids']);
    }

    /**
     * Filter farms based on the requested item ID.
     *
     * @param int $request_id
     * @return array
     */
    public function filterFarms($request_id)
    {
        $item_ids = $this->getRequestedItemIds($request_id);
        $user_assigned_ids = $this->getAllAssignedUserIds();
        $user_farm_ids = $this->getUserFarmLocationIds($user_assigned_ids);
        $user_ids = $this->getUsersWithRequestedItems($user_assigned_ids, $item_ids);

        return [
            'user_assigned_ids' => $user_assigned_ids,
            'item_ids' => $item_ids,
            'user_farm_ids' => $user_farm_ids,
            'user_ids' => $user_ids,
        ];
    }

    /**
     * Get the item IDs that are requested based on the request ID.
     *
     * @param int $request_id The ID of the request item.
     * @return array The IDs of the items requested.
     */
    private function getRequestedItemIds($request_id)
    {
        return IL::where('active_status', 1)
            ->where('request_item_id', $request_id)
            ->pluck('item_id')
            ->toArray();
    }

    /**
     * Retrieve all distinct user IDs assigned to active farm inventories.
     *
     * @return array An array of unique user IDs that are associated with active farm inventories.
     */
    private function getAllAssignedUserIds()
    {
        return FIT::where('active_status', 1)
            ->distinct()
            ->pluck('user_assigned_id')
            ->toArray();
    }

    /**
     * Retrieve the farm location IDs for the specified user IDs.
     *
     * @param array $userIds An array of user IDs to retrieve farm location IDs for.
     * @return array An array of farm location IDs associated with the given user IDs.
     */
    private function getUserFarmLocationIds(array $userIds)
    {
        return User::where('active_status', 1)
            ->whereIn('id', $userIds)
            ->pluck('farm_location_id')
            ->toArray();
    }

    /**
     * Retrieve the user IDs that have the requested items in their inventory.
     *
     * @param array $userIds An array of user IDs to check for requested items.
     * @param array $itemIds An array of item IDs that are requested.
     * @return array An array of user IDs that have the requested items in their inventory.
     */
    private function getUsersWithRequestedItems(array $userIds, array $itemIds)
    {
        return FIT::where('active_status', 1)
            ->whereIn('user_assigned_id', $userIds)
            ->whereIn('item_id', $itemIds)
            ->distinct()
            ->pluck('user_assigned_id')
            ->toArray();
    }

    /**
     * Retrieve the users with the given IDs, including their associated farm locations.
     *
     * @param array $userIds An array of user IDs to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection A collection of users with their associated farm locations.
     */
    private function getFarmsWithLocations(array $userIds)
    {
        return User::with('farmLocation', 'departmentDivision')
            ->whereIn('id', $userIds)
            ->get();
    }

    /**
     * Check the available stock of the selected farm.
     *
     * @return void
     */
    public function checkFarmStock()
    {
        $selected_user_id = $this->available_farm;
        // $this->user = User::findOrFail($selected_user_id);

        // dd($selected_user_id);

        $farm_items = $this->getFarmItems($selected_user_id);

        $this->available_farm_items = [];

        $itemIds = $farm_items->pluck('item_id');
        $items = $this->getItemsWithRelations($itemIds->toArray());
        $requestedQuantities = $this->getRequestedQuantities($itemIds->toArray())->toArray();

        foreach ($farm_items as $item) {
            $itm = $items[$item->item_id];
            $this->available_farm_items[] = $this->formatFarmItemData($item, $itm, $requestedQuantities);
        }
        // dd($this->available_farm_items);
    }

    /**
     * Get the farm items that are available in the selected farm.
     *
     * @param int $userId The id of the selected user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFarmItems($userId)
    {
        return FIT::where('active_status', 1)
            ->where('user_assigned_id', $userId)
            ->whereIn('item_id', $this->filtered_farm['item_ids'])
            ->get();
    }

    /**
     * Retrieve items with their related unit of measurement and item name.
     *
     * @param array $itemIds An array of item IDs to fetch.
     *
     * @return \Illuminate\Support\Collection A collection of items keyed by their ID.
     */
    private function getItemsWithRelations($itemIds)
    {
        return ITM::whereIn('id', $itemIds)
            ->with(['uom:id,terminology,abbreviation', 'itemName:id,item_name'])
            ->get()
            ->keyBy('id');
    }

    /**
     * Retrieve the requested quantities for the specified item IDs.
     *
     * @param array $itemIds An array of item IDs to fetch the requested quantities for.
     *
     * @return \Illuminate\Support\Collection A collection mapping item IDs to their requested quantities.
     */
    private function getRequestedQuantities($itemIds)
    {
        return IL::whereIn('item_id', $itemIds)
            ->where('request_item_id', $this->request_id)
            ->pluck('item_quantity', 'item_id');
    }

    /**
     * Format farm item data to be displayed in the table.
     *
     * @param \App\Models\FarmInventory $item The farm item to format.
     * @param \App\Models\Item $itm The item related to the farm item.
     * @param array $requestedQuantities An array mapping item IDs to requested quantities.
     *
     * @return array An array containing the formatted data.
     */
    private function formatFarmItemData($item, $itm, $requestedQuantities)
    {
        return [
            'farm_item_id' => $item->id,
            'item_id' => $item->item_id,
            'item_name' => $itm->itemName->item_name,
            'quantity_on_stock' => $item->quantity,
            'unit_of_measurement' => $itm->uom->terminology . "-" . $itm->uom->abbreviation,
            'requested_quantity' => $requestedQuantities[$item->item_id] ?? 0,
            'quantity_to_release' => null,
        ];
    }

    /**
     * Validates the form and if valid, proceeds to the next step.
     *
     * @return void
     */
    public function proceed()
    {
        $this->validate();

        try {
            $transactionTypes = $this->request_id != 0
                ? ['checkout - withdrawal', 'Checkout - Withdrawal', 'CHECKOUT - WITHDRAWAL', 'Out', 'out', 'OUT']
                : ['checkout - issuance', 'Checkout - Issuance', 'CHECKOUT - ISSUANCE', 'Out', 'out', 'OUT'];

            $transactionTypeId = TT::whereIn('transaction_type', $transactionTypes)->value('id');

            // Update request item
            if ($this->request_id != 0) {
                $ri = RI::findOrFail($this->request_id);
                $ri->checkout_status = 1;
                $ri->comment .= $this->notes . ' ' . now() . '\.../';
                $approvalTitles = $this->chk_fraction
                    ? ['Item Partially Checked Out', 'ITEM PARTIALLY CHECKED OUT', 'item partially checked out']
                    : ['Item Fully Checked Out', 'ITEM FULLY CHECKED OUT', 'item fully checked out'];

                $approval = Approvals::where('active_status', 1)->whereIn('title', $approvalTitles)->first();
                if (!$approval) {
                    session()->flash('failed', '"Item ' . ($this->chk_fraction ? 'Partially' : 'Fully') . ' Checked Out" Not Yet Set In The Approvals Module.');
                    redirect('/for/approval/list');
                }

                $ri->approval_id = $approval->id;
                $ri->save();
            }

            $selectedQuantities = [];
            $this->user = RI::findOrFail($this->request_id);

            foreach ($this->available_farm_items as $farm_item) {
                // Create transaction
                $transaction = new Transaction([
                    'item_id' => $farm_item['item_id'], //
                    'assigned_by_user_id' => Auth::id(),//
                    'assigned_user_id' => $this->user->requested_by_id,//
                    'transaction_type_id' => $transactionTypeId,
                    'farm_location_id' => $this->user->farm_location_id,
                    'department_division_id' => $this->user->department_division_id,
                    'quantity' => $farm_item['quantity_to_release'],
                    'transaction_date' => now(),
                    'notes' => $this->notes,
                    'active_status' => true,
                    'deleted_status' => false,
                ]);
                $transaction->save();
                AC::logEntry(['Checkout', 'Transaction', '', json_encode($transaction)]);

                $selectedQuantities[] = $farm_item['quantity_to_release'];

                $farmItemModel = FIT::findOrFail($farm_item['farm_item_id']);
                $oldFarmQty = (int) $farmItemModel->quantity;
                $farmItemModel->quantity -= $farm_item['quantity_to_release'];
                $farmItemModel->remarks = 'Inter-Farm Transfer';
                $farmItemModel->save();

                AC::logEntry(['Inter-Farm Transfer', 'Farm Inventory', '', json_encode($farm_item)]);

                // Update/create farm item
                // @param $user_assigned_id, $request_id, $item_id
                $farmItem = FIT::where('active_status', 1)
                    ->where('user_assigned_id', $this->user->requested_by_id)
                    ->where('item_id', $farm_item['item_id'])
                    ->first();

                if ($farmItem) {
                    $farmItemOld = clone $farmItem;
                    $oldFarmQty = (int) $farmItem->quantity;

                    if ($this->request_id != 0) {
                        $farmItem->item_quantity_just_checked_out += $farm_item['quantity_to_release'];
                        $farmItem->request_id = $this->request_id;
                    } else {
                        $farmItem->quantity += $farm_item['quantity_to_release'];
                    }

                    $farmItem->current_quantity = 0;
                    $farmItem->remarks = 'Inter-Farm Transfer';
                    $farmItem->reorder_threshold = 0;
                    $farmItem->save();

                    AC::logEntry(['Inter-Farm Transfer', 'Farm Item', $farmItemOld, $farmItem]);
                } else {
                    $farmItem = new FIT([
                        'item_id' => $farm_item['item_id'],
                        'user_assigned_id' => $this->user->requested_by_id,
                        'quantity' => $this->request_id != 0 ? 0 : $farm_item['quantity_to_release'],
                        'item_quantity_just_checked_out' => $this->request_id != 0 ? $farm_item['quantity_to_release'] : 0,
                        'request_id' => $this->request_id != 0 ? $this->request_id : null,
                        'current_quantity' => 0,
                        'remarks' => 'N/A',
                        'reorder_threshold' => 0,
                        'active_status' => true,
                        'deleted_status' => false,
                        'qr_code' => 'sample.png',
                    ]);
                    $farmItem->save();

                    // $this->generateQRCode($farmItem->id);
                    // $farmItem->qr_code = $this->file_name;
                    // $farmItem->save();

                    AC::logEntry(['Inter-Farm Transfer', 'Farm Item', '', $farmItem]);
                }

                if ($this->request_id < 1) {
                    $historyTypeId = TT::whereIn('transaction_type', $transactionTypes)->value('id');
                    $history = new FIH([
                        'farm_item_id' => $farmItem->id,
                        'transaction_type_id' => $historyTypeId,
                        'previous_quantity' => $oldFarmQty ?? 0,
                        'new_quantity' => $farmItem->quantity,
                        'change_date' => now(),
                        'change_reason' => $this->request_id != 0 ? 'Requested/Withdrawn/Inter-Farm Transfer' : 'Issued',
                        'user_id' => Auth::id(),
                        'active_status' => true,
                        'deleted_status' => false,
                    ]);
                    $history->save();
                }

                // Create item history
                $itemHistory = new IH([
                    'item_id' => $farm_item['item_id'],
                    'transaction_type_id' => $transactionTypeId,
                    'previous_quantity' => $oldFarmQty,
                    'new_quantity' => $farmItemModel->quantity,
                    'old_unit_price' => 0,
                    'new_unit_price' => 0,
                    'change_date' => now(),
                    'user_id' => Auth::id(),
                    'active_status' => true,
                    'deleted_status' => false,
                ]);
                $itemHistory->save();

                AC::logEntry(['Inter-Farm Transfer', 'Inventory History', '', json_encode($itemHistory)]);
            }

            // Update released quantities
            if ($this->request_id != 0) {
                $requestedItems = IL::where('request_item_id', $this->request_id)->get();
                foreach ($requestedItems as $index => $rItem) {
                    $rItem->item_released_quantity += $selectedQuantities[$index];
                    $rItem->item_partially_release_quantity = $selectedQuantities[$index];
                    $rItem->save();
                }
            }

            session()->flash('success', $this->request_id != 0
                ? 'The Inter-Farm Transfer is Succesful!'
                : 'Items have been Checked Out!');

            redirect($this->request_id != 0 ? '/for/approval/list' : '/item/list');

        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed to Transfer!');
            redirect('/item/list');
        }
    }

    /**
     * Real-time Validation
     * updated
     * @param   $propertyName
     * @return  void
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Renders the Livewire component for farm stock check.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.for-approval.farm-stock-check');
    }
}
