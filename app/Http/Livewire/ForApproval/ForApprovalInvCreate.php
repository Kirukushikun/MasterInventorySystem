<?php

namespace App\Http\Livewire\ForApproval;

use Livewire\Component;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;

use App\Http\Controllers\{
    GeneralController as GC,
    AuditController as AC,
    AccessController as ACC,
    WithdrawalSeriesController as WSC
};

use App\Models\{
    RequestItem as RI,
    ItemList as IL,
    Item as ITM,
    FarmInventory as FITM,
    TransactionType as TT,
    FarmItemHistory as FIH,
    Approvals,
    ItemName as ITNAME,
    Category as CT,
    SubCategory as SCT,
    Product as PRD,
    User,
    Location as LC,
    FarmLocation as FL,
    DepartmentDivision as DD,
    UnitOfMeasurement as UOM,
    InventoryHistory as IH
};


class ForApprovalInvCreate extends Component
{
    public $selectedItems = [];
    public $sel_items = [];
    public $data;
    public $approvers_id;
    public $ris;

    protected $listeners = ['approve', 'deny'];

    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $this->approvers_id = $user->id;
        $dd_id = $user->department_division_id;
        $user_role = $user->role;

        $itemData = ITM::where('active_status', 1)->where('approval_id', 5)->get();

        $this->data = [];

        if ($itemData->count() > 0) {
            $ctr = 1;
            foreach ($itemData as $al) {
                $this->ris[] = $al->id;
                $titleMap = [
                    "Approved" => "badge-primary",
                    "APPROVED" => "badge-primary",
                    "approved" => "badge-primary",
                    "Approve" => "badge-primary",
                    "APPROVE" => "badge-primary",
                    "approve" => "badge-primary",
                    "Denied" => "badge-danger",
                    "DENIED" => "badge-danger",
                    "denied" => "badge-danger",
                    "Rejected" => "badge-danger",
                    "REJECTED" => "badge-danger",
                    "rejected" => "badge-danger",
                    "For Approval" => "badge-warning",
                    "FOR APPROVAL" => "badge-warning",
                    "for approval" => "badge-warning"
                ];

                $titleToCheck = (Approvals::where('id', $al->approval_id)->first() ?
                                 Approvals::where('id', $al->approval_id)->first()->title : "for approval");

                $status = '';

                if (isset($titleMap[$titleToCheck])) {
                    $badgeClass = $titleMap[$titleToCheck];
                    $status = '<span class="badge ' . $badgeClass . '">' . strtoupper($titleToCheck) . '</span>';
                }

                $this->data[] = [
                    'id'                => $ctr,
                    'item_image'        => '
                        <img src="' . asset('photos/' . $al->item_image) . '" alt="Click to zoom" class="img-thumbnail imageZoomButton" data-imageurl="' . asset('photos/' . $al->item_image) . '" style="width: 80px; height: 80px; max-width: 80px; max-height: 80px;">
                    ',
                    'category'          => CT::findorfail($al->category_id)->category_name,
                    'subcategory'       => SCT::findorfail($al->subcategory_id)->subcategory_name,
                    'product'           => PRD::findorfail($al->product_id)->product_name,
                    'item_name'         => ITNAME::findorfail($al->item_name_id)->item_name,
                    'location'          => LC::findorfail($al->location_id)->location_name,
                    'uom'               => UOM::findorfail($al->uom_id)->terminology,
                    'quantity_on_hand'  => $al->quantity,
                    'remaining_one'     => $al->quantity,
                    'reorder_threshold' => $al->reorder_threshold,
                    'purchase_cost'     => 'PHP ' . number_format($al->purchase_cost, 2, '.', ','),
                    'purchase_date' => $al->purchase_date,
                    'expiry_date' => $al->expiry_date,
                    'action' =>
                    (ACC::checkAccess(Auth::id(), 'forapprovals_inv_create_approve') ?
                        '<button data-value="' . $al->id . '" id="showNotifCreateInv' . $al->id . '" class="btn btn-primary" ' .
                        (in_array($titleToCheck, ["For Approval", "FOR APPROVAL", "for approval"]) ? '' : 'disabled') . '>
                            <i class="fas fa-thumbs-up"></i> Approve
                        </button> '
                        : "") .
                    (ACC::checkAccess(Auth::id(), 'forapprovals_inv_create_deny') ?
                        '<button data-value="' . $al->id . '" id="showNotifCreateInvDeny' . $al->id . '" class="btn btn-danger" ' .
                        (in_array($titleToCheck, ["For Approval","FOR APPROVAL","for approval"]) ? '' : 'disabled') . '>
                            <i class="fas fa-times"></i> Deny
                        </button> '
                        : "") .
                    (ACC::checkAccess(Auth::id(), 'forapprovals_inv_create_approve') == false && ACC::checkAccess(Auth::id(), 'forapprovals_inv_create_deny') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                ];
                $ctr++;
            }
        }
    }

    /**
     * A function to approve a specific value.
     *
     * @param datatype $value_to_send Value to be approved
     * @throws ValidationException Failed to approve
     * @throws \Exception General failure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($value_to_send)
    {
        try {
            $itd = ITM::findOrFail($value_to_send);
            $old_value = clone $itd;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['Approved', 'APPROVED', 'approved', 'Approve', 'APPROVE', 'approve'])
                ->first();

            if ($approval) {
                $itd->approval_id = $approval->id;
                $itd->save();

                // Assigning variables with null coalescing operator
                $quantity = $itd->quantity ?? 0;
                $purchase_date = $itd->purchase_date ?? null;
                $expiry_date = $itd->expiry_date ?? null;
                $purchase_cost = $itd->purchase_cost ?? null;
                $unit_price = $itd->current_quantity != 0 ? ($itd->purchase_cost / $itd->current_quantity) : null;
                $remarks = $itd->remarks ?? 'N/A';

                // Log Entry
                $log_entry = [
                    'Create',
                    'Item',
                    '',
                    $itd,
                ];

                AC::logEntry($log_entry);

                // Transaction Type
                $transaction_type = ["CREATE", "ADD", "Add", "Create", "add", "create"];
                $transactionTypeId = TT::whereIn('transaction_type', $transaction_type)->first()->id;

                // Item History
                $item_history = new IH();
                $item_history->item_id = $itd->id;
                $item_history->transaction_type_id = $transactionTypeId;
                $item_history->new_quantity = $quantity;
                $item_history->new_unit_price = $unit_price;
                $item_history->new_purchase_date = $purchase_date;
                $item_history->new_expiry_date = $expiry_date;
                $item_history->change_date = now(); // Simplified to use Laravel's now()
                $item_history->user_id = Auth::id();
                $item_history->active_status = true;
                $item_history->deleted_status = false;
                $item_history->save();

                session()->flash('success', 'The Item Created has been Approved and Available in the Inventory!');
                return redirect('/for/approval/list');
            } else {
                session()->flash('failed', 'Approval not found in the Approvals Module.');
                return redirect('/for/approval/list');
            }

        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed to Approve!');
        } catch (\Exception $e) {
            session()->flash('failed', $e->getMessage()); // Corrected to get the message
        }
        return redirect('/for/approval/list');
    }

    public function deny($value_to_send)
    {
        try {
            $itd = ITM::findOrFail($value_to_send);
            $old_value = clone $itd;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['Denied', 'DENIED', 'denied', 'Rejected', 'REJECTED', 'rejected'])
                ->first();

            if ($approval) {
                $itd->approval_id = $approval->id;
                $itd->active_status = 0;
                $itd->save();

                $log_entry = [
                    'For Approvals',
                    'Denied Creation - Inventory',
                    $old_value,
                    $itd,
                ];
                AC::logEntry($log_entry);

                session()->flash('success', 'This Request has been Denied!');
            } else {
                session()->flash('failed', 'Denial not found in the Approvals Module.');
            }
        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed to Deny Request!');
        } catch (\Exception $e) {
            session()->flash('failed', 'An error occurred while processing the denial.');
        }
        return redirect('/for/approval/list');
    }

    public function render()
    {
        return view('livewire.for-approval.for-approval-inv-create');
    }
}
