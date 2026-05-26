<?php

namespace App\Http\Livewire\ForApproval;

use Livewire\Component;
use App\Models\RequestItem as RI;
use App\Models\ItemList as IL;
use App\Models\Item as ITM;
use App\Models\FarmInventory as FITM;
use App\Models\TransactionType as TT;
use App\Models\FarmItemHistory as FIH;
use App\Models\Approvals;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;
use App\Http\Controllers\WithdrawalSeriesController as WSC;

use App\Models\ItemName as ITNAME;
use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\User;
use App\Models\Location as LC;
use App\Models\FarmLocation as FL;
use App\Models\DepartmentDivision as DD;
use App\Models\UnitOfMeasurement as UOM;

class ForApprovalInventoryUpdate extends Component
{

    public $selectedItems = [];
    public $sel_items = [];
    public $data;
    public $approvers_id;
    public $ris;
    public $id_holder;

    protected $listeners = ['approve', 'deny'];

    public function mount()
    {

        // $this->listeners = ['approve', 'deny'];
        $user = User::findOrFail(Auth::id());
        $this->approvers_id = $user->id;
        $dd_id = $user->department_division_id;
        $user_role = $user->role;

        $itemData = FITM::where('active_status', 1)->where('approval_id', 5)->get();
        // $itemData = FITM::where('active_status', 1)->whereNotNull('approval_id')->get();

        $this->data = [];

        if($itemData->count() > 0) {
            $ctr = 1;
            if ($user->role == "cenwh keeper" || $user_role == "superuser") {
                foreach($itemData as $itd) {
                    $this->ris[] = $itd->id;
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

                    $titleToCheck = (Approvals::where('id', $itd->approval_id)->first() ?
                                     Approvals::where('id', $itd->approval_id)->first()->title : "for approval");

                    $status = '';

                    if (isset($titleMap[$titleToCheck])) {
                        $badgeClass = $titleMap[$titleToCheck];
                        $status = '<span class="badge ' . $badgeClass . '">' . strtoupper($titleToCheck) . '</span>';
                        $this->data[] = [
                            'id'                => $ctr,
                            'item_name'         => ITNAME::findorfail(ITM::findorfail($itd->item_id)->item_name_id)->item_name,
                            'category'          => CT::findorfail(ITM::findorfail($itd->item_id)->category_id)->category_name,
                            'subcategory'       => SCT::findorfail(ITM::findorfail($itd->item_id)->subcategory_id)->subcategory_name,
                            'product'           => PRD::findorfail(ITM::findorfail($itd->item_id)->product_id)->product_name,
                            'uom'               => UOM::findorfail(ITM::findorfail($itd->item_id)->uom_id)->terminology,
                            'quantity'          => $itd->quantity,
                            'quantity_to_remove'=> $itd->quantity_to_remove,
                            'status'            => $status,
                            'date'              => $itd->updated_at,
                            'action'            =>
                            (ACC::checkAccess(Auth::id(), 'forapprovals_update_approve') ?
                                '<button data-value="' . $itd->id . '" id="showNotifUpdate' . $itd->id . '" class="btn btn-primary" ' .
                                (in_array($titleToCheck, ["For Approval", "FOR APPROVAL", "for approval"]) ? '' : 'disabled') . '>
                                    <i class="fas fa-thumbs-up"></i> Approve
                                </button> '
                                : "") .
                            (ACC::checkAccess(Auth::id(), 'forapprovals_update_deny') ?
                                '<button data-value="' . $itd->id . '" id="showNotifUpdateDeny' . $itd->id . '" class="btn btn-danger" ' .
                                (in_array($titleToCheck, ["For Approval","FOR APPROVAL","for approval"]) ? '' : 'disabled') . '>
                                    <i class="fas fa-times"></i> Deny
                                </button> '
                                : "") .
                            (ACC::checkAccess(Auth::id(), 'forapprovals_update_approve') == false && ACC::checkAccess(Auth::id(), 'forapprovals_update_deny') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                        ];
                        $ctr++;
                    }

                }
            } else{
                foreach($itemData as $itd) {
                    $dptmnt = User::findorfail($itd->user_assigned_id)->department_division_id;

                    if($dd_id == $dptmnt){
                        $this->ris[] = $itd->id;
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

                        $titleToCheck = (Approvals::where('id', $itd->approval_id)->first() ?
                                         Approvals::where('id', $itd->approval_id)->first()->title : "for approval");

                        $status = '';

                        if (isset($titleMap[$titleToCheck])) {
                            $badgeClass = $titleMap[$titleToCheck];
                            $status = '<span class="badge ' . $badgeClass . '">' . strtoupper($titleToCheck) . '</span>';
                        }

                        $this->data[] = [
                            'id'                => $ctr,
                            'item_name'         => ITNAME::findorfail(ITM::findorfail($itd->item_id)->item_name_id)->item_name,
                            'category'          => CT::findorfail(ITM::findorfail($itd->item_id)->category_id)->category_name,
                            'subcategory'       => SCT::findorfail(ITM::findorfail($itd->item_id)->subcategory_id)->subcategory_name,
                            'product'           => PRD::findorfail(ITM::findorfail($itd->item_id)->product_id)->product_name,
                            'uom'               => UOM::findorfail(ITM::findorfail($itd->item_id)->uom_id)->terminology,
                            'quantity'          => $itd->quantity,
                            'quantity_to_remove'=> $itd->quantity_to_remove,
                            'status'            => $status,
                            'date'              => $itd->updated_at,
                            'action'            =>
                            (ACC::checkAccess(Auth::id(), 'forapprovals_update_approve') ?
                                '<button data-value="' . $itd->id . '" id="showNotifUpdate' . $itd->id . '" class="btn btn-primary" ' .
                                (in_array($titleToCheck, ["For Approval", "FOR APPROVAL", "for approval"]) ? '' : 'disabled') . '>
                                    <i class="fas fa-thumbs-up"></i> Approve
                                </button> '
                                : "") .
                            (ACC::checkAccess(Auth::id(), 'forapprovals_update_deny') ?
                                '<button data-value="' . $itd->id . '" id="showNotifUpdateDeny' . $itd->id . '" class="btn btn-danger" ' .
                                (in_array($titleToCheck, ["For Approval","FOR APPROVAL","for approval"]) ? '' : 'disabled') . '>
                                    <i class="fas fa-times"></i> Deny
                                </button> '
                                : "") .
                            (ACC::checkAccess(Auth::id(), 'forapprovals_update_approve') == false && ACC::checkAccess(Auth::id(), 'forapprovals_update_deny') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                        ];
                        $ctr++;
                    }
                }
            }
        }
    }

    public function approve($value_to_send)
    {
        try {
            $itd = FITM::findOrFail($value_to_send);
            $old_value = clone $itd;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['Approved', 'APPROVED', 'approved', 'Approve', 'APPROVE', 'approve'])
                ->first();

            if ($approval) {
                $itd->approval_id = $approval->id;
                $previous_quantity = $itd->quantity;
                $quan             = $itd->quantity - $itd->quantity_to_remove;
                $itd->quantity    = $quan;
                $itd->save();

                $log_entry = [
                    'For Approvals',
                    'Approved Update - Farm Level Inventory',
                    $old_value,
                    $itd,
                ];
                AC::logEntry($log_entry);


                $transaction_type = ["UPDATE", "EDIT", "Edit", "Update", "edit", "update"];

                $item_history  = new FIH();
                $item_history->farm_item_id        = $value_to_send;
                $item_history->transaction_type_id = TT::whereIn('transaction_type', $transaction_type)->get()->first()->id;

                $item_history->previous_quantity   = $previous_quantity;
                $item_history->new_quantity        = $quan;

                $item_history->change_date         = $itd->updated_at;
                $item_history->change_reason       = $itd->remarks;
                $item_history->user_id             = $itd->user_assigned_id;

                $item_history->active_status       = true;
                $item_history->deleted_status      = false;
                $item_history->save();

                session()->flash('success', 'This Update has been Approved!');
                // return redirect('/for/approval/list');
            } else {
                session()->flash('failed', 'Approval not found in the Approvals Module.');
                // return redirect('/for/approval/list');
            }

        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed to Approve!');
        } catch (\Exception $e) {
            session()->flash('failed', 'An error occurred while processing the approval.');
        }
        return redirect('/for/approval/list');
    }

    public function deny($value_to_send)
    {
        try {
            $itd = FITM::findOrFail($value_to_send);
            $old_value = clone $itd;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['Denied', 'DENIED', 'denied', 'Rejected', 'REJECTED', 'rejected'])
                ->first();

            if ($approval) {
                $itd->approval_id = $approval->id;
                $itd->save();

                $log_entry = [
                    'For Approvals',
                    'Denied Update - Farm Level Inventory',
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
        return view('livewire.for-approval.for-approval-inventory-update');
    }
}
