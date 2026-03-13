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

use App\Models\ItemName as ITNAME;
use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\User;
use App\Models\Location as LC;
use App\Models\FarmLocation as FL;
use App\Models\DepartmentDivision as DD;
use App\Models\UnitOfMeasurement as UOM;
use Illuminate\Validation\ValidationException;

class ForApprovalList extends Component
{

    public $selectedItems = [];
    public $sel_items = [];
    public $data;
    public $data2;
    public $dat2;
    public $approvers_id;
    public $ris1;
    public $ris2;
    public $testIfArrayHaveSameElements = false;

    protected $listeners = ['approved', 'denied', 'for_release', 'in_transit', 'received'];

    /**
     * Checks if all elements in the given array are the same.
     *
     * @param array $array The array to be checked.
     * @return bool Returns true if all elements are the same, false otherwise.
     */
    public function areArrayElementsSame($array) {
        return array_reduce($array, function ($carry, $element) {
            $values = explode(' ', $element);
            return $carry && count($values) === 2 && $values[0] === $values[1];
        }, true);
    }

    public function mount()
    {

        // $this->listeners = ['approve', 'deny'];
        $user = User::findOrFail(Auth::id());
        $this->approvers_id = $user->id;
        $dd_id = $user->department_division_id;
        $user_role = $user->role;

        $table_head = '
            <table class="table table-responsive table-hover text-wrap" style="width: 100%;">
                <thead>
                    <tr>
                        <th><u>ITEM #</u></th>
                        <th><u>ITEM IMAGE</u></th>
                        <th><u>ITEM NAME</u></th>
                        <th><u>PRODUCT</u></th>
                        <th><u>U/M</u></th>
                        <th><u>QUANTITY</u></th>
                        <th><u>QUANTITY RELEASED</u></th>
                    </tr>
                </thead>
                <tbody>
        ';
                        // <th><u>CATEGORY</u></th>
                        // <th><u>SUB CATEGORY</u></th>
        $table_foot = '
                </tbody>
            </table>
        ';

        if ($user_role == "cenwh keeper" || $user_role == "superuser") {
            $ri = RI::where('active_status', 1)->get();
        } else {
            $ri = RI::where('active_status', 1)
                    ->where('department_division_id', $dd_id)
                    ->get();
        }


        $this->data = [];
        $this->data2 = [];

        if($ri->count() > 0) {
            $ctr = 1;
            foreach($ri as $ris) {
                $this->ris1[] = $ris->id;
                // $this->ris2[] = $ris;
                $item_lists = IL::where('request_item_id', $ris->id)->where('active_status', 1)->get();
                $it_list = null;
                $idss = '';
                $qtys = '';

                $idss = rtrim($idss, ',');
                $qtys = rtrim($qtys, ',');

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
                    "For Release" => "badge-info",
                    "FOR RELEASE" => "badge-info",
                    "for release" => "badge-info",
                    "In-Transit" => "badge-secondary",
                    "IN-TRANSIT" => "badge-secondary",
                    "in-transit" => "badge-secondary",
                    "Received" => "badge-success",
                    "RECEIVED" => "badge-success",
                    "received" => "badge-success",
                    "For Approval" => "badge-warning",
                    "FOR APPROVAL" => "badge-warning",
                    "for approval" => "badge-warning",
                    'Item Partially Checked Out' => "badge-primary",
                    'ITEM PARTIALLY CHECKED OUT' => "badge-primary",
                    'item partially checked out' => "badge-primary",
                    'Item Fully Checked Out' => "badge-success",
                    'ITEM FULLY CHECKED OUT' => "badge-success",
                    'item fully checked out' => "badge-success",
                    'Item Partially Checked Out Are In-Transit' => "badge-secondary",
                    'ITEM PARTIALLY CHECKED OUT ARE IN-TRANSIT' => "badge-secondary",
                    'item partially checked out are in-transit' => "badge-secondary",
                    'Item Partially Checked Out Are For Release' => "badge-info",
                    'ITEM PARTIALLY CHECKED OUT ARE FOR RELEASE' => "badge-info",
                    'item partially checked out are for release' => "badge-info",
                    'Item Partially Checked Out Are Received' => "badge-success",
                    'ITEM PARTIALLY CHECKED OUT ARE RECEIVED' => "badge-success",
                    'item partially checked out are received' => "badge-success"
                ];

                $titleToCheck = Approvals::where('id', $ris->approval_id)->first()->title;

                $status = '';

                if (isset($titleMap[$titleToCheck])) {
                    $badgeClass = $titleMap[$titleToCheck];
                    $status = '<span class="badge ' . $badgeClass . '">' . strtoupper($titleToCheck) . '</span>';
                }


                $test_if_complete = [];
                foreach($item_lists as $key => $il){
                    $item_name = ITNAME::findorfail($il->item_id);

                    $idss .= $il->item_id . ",";
                    $qtys .= $il->item_quantity . ",";

                    $it_list .= '
                        <tr>
                            <td>' . ($key + 1) . '</td>
                            <td><img src="' . asset('photos/' . ITM::findorfail($il->item_id)->item_image) . '" alt="Click to zoom" class="img-thumbnail imageZoomButton" data-imageurl="' . asset('photos/' . ITM::findorfail($il->item_id)->item_image) . '" style="width: 80px; height: 80px; max-width: 80px; max-height: 80px;"></td>
                            <td>' . (empty($item_name->item_name) ? "-" : strtoupper($item_name->item_name)) . '</td>
                            <td>' . (empty(PRD::findorfail($item_name->product_id)->product_name) ? "-" : strtoupper(PRD::findorfail($item_name->product_id)->product_name)) . '</td>
                            <td>' . (empty($il->uom_id) ? "-" : strtoupper(UOM::findorfail($il->uom_id)->abbreviation)) . '</td>
                            <td>' . (empty($il->item_quantity) ? "-" : $il->item_quantity) . '</td>
                            <td>' . (empty($il->item_released_quantity) ? "-" : $il->item_released_quantity) . '</td>
                        </tr>'
                    ;
                    // <td>' . (empty(CT::findorfail($item_name->category_id)->category_name) ? "-" : strtoupper(CT::findorfail($item_name->category_id)->category_name)) . '</td>
                    // <td>' . (empty(SCT::findorfail($item_name->subcategory_id)->subcategory_name) ? "-" : strtoupper(SCT::findorfail($item_name->subcategory_id)->subcategory_name)) . '</td>

                    $test_if_complete[] = (empty($il->item_quantity) ? "-" : $il->item_quantity) . " " . (empty($il->item_released_quantity) ? "-" : $il->item_released_quantity);

                    // $this->data2[] = [
                    //     'id' => $ctr,
                    //     'series_number' => strtoupper($ris->series_number),
                    //     'requested_by' => strtoupper(User::findorfail($ris->requested_by_id)->name),
                    //     'location' => FL::findorfail($ris->farm_location_id)->farm_location,
                    //     'department_division' => DD::findorfail($ris->department_division_id)->department_division,
                    //     'status' => $status,
                    //     'item_name' => (empty($item_name->item_name) ? "-" : strtoupper($item_name->item_name)),
                    //     'category' => (empty(CT::findorfail($item_name->category_id)->category_name) ? "-" : strtoupper(CT::findorfail($item_name->category_id)->category_name)),
                    //     'subcategory' => (empty(SCT::findorfail($item_name->subcategory_id)->subcategory_name) ? "-" : strtoupper(SCT::findorfail($item_name->subcategory_id)->subcategory_name)),
                    //     'product' => (empty(PRD::findorfail($item_name->product_id)->product_name) ? "-" : strtoupper(PRD::findorfail($item_name->product_id)->product_name)),
                    //     'uom' => (empty($il->uom_id) ? "-" : strtoupper(UOM::findorfail($il->uom_id)->abbreviation)),
                    //     'quantiy' => (empty($il->item_quantity) ? "-" : $il->item_quantity),
                    //     'date_requested' => $ris->date_requested,
                    //     'date_needed' => $ris->date_needed,
                    // ];
                }
                $this->testIfArrayHaveSameElements = $this->areArrayElementsSame($test_if_complete);
                if ($this->areArrayElementsSame($test_if_complete) && in_array($titleToCheck, ["ITEM PARTIALLY CHECKED OUT", "item partially checked out", "Item Partially Checked Out"])) {
                    $status = '<span class="badge badge-success">ITEM FULLY CHECKED OUT</span>';
                }

                $this->dat2[$ris->id] = [
                    'requested_by' => strtoupper(User::findorfail($ris->requested_by_id)->name),
                    'location' => FL::findorfail($ris->farm_location_id)->farm_location,
                    'abbrvtn' => FL::findorfail($ris->farm_location_id)->abbreviation,
                    'department_division' => DD::findorfail($ris->department_division_id)->department_division,
                    'sender_name' => strtoupper(User::findorfail(Auth::id())->name),
                    // 'approvers_name' => strtoupper(User::findorfail($ris->approver_id)->name),
                    'signature' => asset('img/millet_e_sign.png'),
                    'approvers_signature' => asset('img/isla_e_sign.png'),
                ];

                // Explode the string using the pattern '\.../'
                $lines = explode('\.../', strtoupper($ris->comment));
                $strLines = "";
                // Print each line
                foreach ($lines as $line) {
                    $strLines .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>CENWH:</strong> ' . ($line == '' ? '<strong>...</strong>' : $line) . '<br><br>';
                }

                $this->data[] = [
                    'id' => $ctr,
                    'series_number' => strtoupper($ris->series_number),
                    'requested_by' => strtoupper(User::findorfail($ris->requested_by_id)->name),
                    'location' => FL::findorfail($ris->farm_location_id)->farm_location,
                    'department_division' => DD::findorfail($ris->department_division_id)->department_division,
                    'status' => $status,
                    'items_requested' => '
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#show' . $ris->id . '">
                          SHOW ITEM REQUESTED
                        </button>' . '

                        <!-- Modal -->
                        <div class="modal fade" id="show' . $ris->id . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 80%;">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Item List</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                ' . $table_head . $it_list . $table_foot . '
                              </div>
                              <div class="modal-footer ml-auto">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    ',
                    'date_requested' => $ris->date_requested,
                    'date_needed' => $ris->date_needed,
                    'comment' => '
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#showComment' . $ris->id . '">
                        SHOW COMMENT
                        </button>' . '

                        <!-- Modal -->
                        <div class="modal fade" id="showComment' . $ris->id . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 80%;">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" id="exampleModalLongTitle">Comment</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h5>' . $strLines . '</h5>
                            </div>
                            <div class="modal-footer ml-auto">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                            </div>
                            </div>
                        </div>
                        </div>
                    ',
                    'attachment' => '
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#showFilePdfAndDownload' . $ris->id . '">
                        SHOW FILE<i class="fas fa-file-pdf"></i> and DOWNLOAD<i class="fas fa-download"></i>
                        </button>' . '

                        <!-- Modal -->
                        <div class="modal fade" id="showFilePdfAndDownload' . $ris->id . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 80%;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title" id="exampleModalLongTitle">PDF File Preview and Download</h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <embed src="' . asset('pdfs/' . $ris->jl_pdf . '.pdf') . '" type="application/pdf" width="100%" height="600px">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer ml-auto">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ',
                    'action' =>
                        (ACC::checkAccess(Auth::id(), 'forapprovals_approve') ?
                            '<button data-value="' . $ris->id . '" id="showNotif' . $ris->id . '" class="btn btn-primary" ' .
                            (in_array($titleToCheck, ["For Approval", "FOR APPROVAL", "for approval"]) ? '' : 'disabled') . '>
                                <i class="fas fa-thumbs-up"></i> Approve
                            </button> '
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'forapprovals_checkout') ?
                            '<a id="checkout-btn' . $ris->id . '" href="' . route('item.list') . '?idss=[' . $idss . ']&reqid=' . $ris->requested_by_id . '&reqqty=[' . $qtys . ']&requestid=' . $ris->id . '" class="btn btn-primary ' .
                            (in_array($titleToCheck, [
                                "Approved", "APPROVED", "approved",
                                "Item Partially Checked Out", "ITEM PARTIALLY CHECKED OUT", "item partially checked out",
                                'Item Partially Checked Out Are In-Transit', 'ITEM PARTIALLY CHECKED OUT ARE IN-TRANSIT', 'item partially checked out are in-transit',
                                'Item Partially Checked Out Are For Release', 'ITEM PARTIALLY CHECKED OUT ARE FOR RELEASE', 'item partially checked out are for release',
                                'Item Partially Checked Out Are Received', 'ITEM PARTIALLY CHECKED OUT ARE RECEIVED', 'item partially checked out are received']) && !$this->areArrayElementsSame($test_if_complete) ? '' : 'disabled') . '">
                                <i class="fas fa-shopping-cart"></i> Checkout Item
                            </a> ' : '' ) .
                        (ACC::checkAccess(Auth::id(), 'forapprovals_forrelease') ?
                            '<button data-value="' . $ris->id . '" id="showNotifForRelease' . $ris->id . '" class="btn btn-info" ' .
                            (
                                in_array($titleToCheck, ["Item Fully Checked Out", "ITEM FULLY CHECKED OUT", "item fully checked out", "Item Partially Checked Out", "ITEM PARTIALLY CHECKED OUT", "item partially checked out"]) ||
                                $status == '<span class="badge badge-success">ITEM FULLY CHECKED OUT</span>' ? '' : 'disabled'
                            ) . '>
                                <i class="fas fa-hand-holding-box"></i> For Release
                            </button> '
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'forapprovals_intransit') ?
                            '<button data-value="' . $ris->id . '" id="showNotifInTransit' . $ris->id . '" class="btn btn-secondary" ' .
                            (in_array($titleToCheck, [
                                "For Release", "FOR RELEASE", "for release",
                                'Item Partially Checked Out Are For Release', 'ITEM PARTIALLY CHECKED OUT ARE FOR RELEASE', 'item partially checked out are for release']) ? '' : 'disabled') . '>
                                <i class="fas fa-truck"></i> In-Transit
                            </button> '
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'forapprovals_received') ?
                            '<button data-value="' . $ris->id . '" id="showNotifReceived' . $ris->id . '" class="btn btn-success" ' .
                            (in_array($titleToCheck, [
                                "In-Transit", "IN-TRANSIT", "in-transit",
                                'Item Partially Checked Out Are In-Transit', 'ITEM PARTIALLY CHECKED OUT ARE IN-TRANSIT', 'item partially checked out are in-transit'
                            ]) ? '' : 'disabled') . ' ' .
                            (in_array($titleToCheck, [
                                "In-Transit", "IN-TRANSIT", "in-transit",
                                'Item Partially Checked Out Are In-Transit', 'ITEM PARTIALLY CHECKED OUT ARE IN-TRANSIT', 'item partially checked out are in-transit'
                            ]) ? '' : 'style="color: #94d82d;"') . '>
                                <i class="fas fa-inbox-in"></i> Received
                            </button> '
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'forapprovals_deny') ?
                            '<button data-value="' . $ris->id . '" id="showNotifDeny' . $ris->id . '" class="btn btn-danger" ' .
                            (in_array($titleToCheck, ["For Approval","FOR APPROVAL","for approval"]) ? '' : 'disabled') . ' ' .
                            (in_array($titleToCheck, ["For Approval","FOR APPROVAL","for approval"]) ? '' : 'style="color: #ffa8a8;"') . '>
                                <i class="fas fa-times"></i> Deny
                            </button> '
                            : "") .
                        (ACC::checkAccess(Auth::id(), 'forapprovals_approve') == false && ACC::checkAccess(Auth::id(), 'forapprovals_deny') == false ? '<a class="btn btn-info disabled">N/A</a>' : "")
                ];
                $ctr++;
            }
        }else{
            $this->data[] = [
                'id'                         => "NO DATA AVAILABLE!",
                'series_number'              => "-",
                'requested_by'               => "-",
                'location'                   => "-",
                'department_division'        => "-",
                'status'                     => "-",
                'items_requested'            => '
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#show1">
                      NO DATA AVAILABLE!
                    </button>
                ',
                'date_requested'             => "-",
                'date_needed'                => "-",
                'comment'                    => "-",
                'attachment'                 => "-",
                'action' =>
                    '<button data-value="1" id="showNotif1" class="btn btn-primary" disabled>
                        <i class="fas fa-thumbs-up"></i> Approve
                    </button>
                    <a id="checkout-btn1" href="" class="btn btn-primary disabled">
                        <i class="fas fa-shopping-cart"></i> Checkout Item
                    </a>
                    <button data-value="1" id="showNotifForRelease1" class="btn btn-info" disabled>
                        <i class="fas fa-hand-holding-box"></i> For Release
                    </button>
                    <button data-value="1" id="showNotifInTransit1" class="btn btn-secondary" disabled>
                        <i class="fas fa-truck"></i> In-Transit
                    </button>
                    <button data-value="1" id="showNotifReceived1" class="btn btn-success" disabled>
                        <i class="fas fa-inbox-in"></i> Received
                    </button>
                    <button data-value="1" id="showNotifDeny1" class="btn btn-danger" disabled>
                        <i class="fas fa-times"></i> Deny
                    </button> '
            ];
        }
    }

    /**
     * In transit function.
     *
     * @param datatype $value_to_send The value to send.
     * @throws ValidationException If validation fails.
     * @throws \Exception If an error occurs while processing the approval.
     * @return redirect The redirect to '/for/approval/list'.
     */
    public function in_transit($value_to_send)
    {
        try {
            $ris = RI::findOrFail($value_to_send);
            $old_value = clone $ris; // Clone the $ris object to preserve the old value
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ($this->testIfArrayHaveSameElements ?
                    ['In-Transit', 'IN-TRANSIT', 'in-transit'] :
                    ['Item Partially Checked Out Are In-Transit', 'ITEM PARTIALLY CHECKED OUT ARE IN-TRANSIT', 'item partially checked out are in-transit']
                ))->first();

            if ($approval) {
                $ris->approval_id = $approval->id;
                $ris->approver_id = $this->approvers_id;
                $ris->save();

                $log_entry = [
                    'In-Transit',
                    'Request In-Transit',
                    $old_value,
                    $ris,
                ];
                AC::logEntry($log_entry);

                session()->flash('success', 'The Item(s) Requested Is Now In-Transit!');
            } else {
                session()->flash('failed', 'In-Transit Not Yet Set In The Approvals Module.');
            }

        } catch (ValidationException $e) {
            // Validation failed, handle the error
            session()->flash('failed', 'Failed!');
            // return redirect('/for/approval/list');
        } catch (\Exception $e) {
            // Handle other exceptions here
            session()->flash('failed', 'An error occurred while processing the approval.');
            // return redirect('/for/approval/list');
        }
        return redirect('/for/approval/list');
    }

    /**
     * Updates the approval status of a record and logs the approval action.
     *
     * @param int $value_to_send The ID of the record to be approved.
     * @throws ValidationException If the validation fails.
     * @return void
     */
    public function approved($value_to_send)
    {
        try {
            $ris = RI::findOrFail($value_to_send);
            $old_value = clone $ris;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['Approved', 'APPROVED', 'approved', 'Approve', 'APPROVE', 'approve'])
                ->first();

            if ($approval) {
                $ris->approval_id = $approval->id;
                $ris->approver_id = $this->approvers_id;
                $ris->save();

                $log_entry = [
                    'Approved',
                    'Approve Request',
                    $old_value,
                    $ris,
                ];
                AC::logEntry($log_entry);

                session()->flash('success', 'The Item(s) Requested has been Approved!');
            } else {
                session()->flash('failed', 'Approval not found in the Approvals Module.');
            }

        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed to Approve!');
            // return redirect('/for/approval/list');
        } catch (\Exception $e) {
            session()->flash('failed', 'An error occurred while processing the approval.');
            // return redirect('/for/approval/list');
        }
        return redirect('/for/approval/list');
    }

    /**
     * A function that handles denial of a value.
     *
     * @param mixed $value_to_send The value to be denied.
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     * @return \Illuminate\Http\RedirectResponse Redirects to the approval list.
     */
    public function denied($value_to_send)
    {
        try {
            $ris = RI::findOrFail($value_to_send);
            $old_value = clone $ris;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['Denied', 'DENIED', 'denied', 'Rejected', 'REJECTED', 'rejected'])
                ->first();

            if ($approval) {
                $ris->approval_id = $approval->id;
                $ris->approver_id = $this->approvers_id;
                $ris->save();

                $log_entry = [
                    'Denied',
                    'Request Denied',
                    $old_value,
                    $ris,
                ];
                AC::logEntry($log_entry);

                session()->flash('success', 'The Item(s) Requested has been Denied!');
            } else {
                session()->flash('failed', 'Denial not found in the Approvals Module.');
            }

        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed to Deny Request!');
            // return redirect('/for/approval/list');
        } catch (\Exception $e) {
            session()->flash('failed', 'An error occurred while processing the denial.');
            // return redirect('/for/approval/list');
        }
        return redirect('/for/approval/list');
    }

    /**
     * Executes the for_release functionality.
     *
     * @param mixed $value_to_send The value to send for the for_release functionality.
     * @throws ValidationException If a validation error occurs.
     * @throws \Exception If an error occurs while processing the release request.
     * @return \Illuminate\Http\RedirectResponse The redirect response to the /for/approval/list route.
     */
    public function for_release($value_to_send)
    {
        try {
            $ris = RI::findOrFail($value_to_send);
            $old_value = clone $ris;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ($this->testIfArrayHaveSameElements ? ['For Release', 'FOR RELEASE', 'for release'] : ['Item Partially Checked Out Are For Release', 'ITEM PARTIALLY CHECKED OUT ARE FOR RELEASE', 'item partially checked out are for release'] ))
                ->first();

            if ($approval) {
                $ris->approval_id = $approval->id;
                $ris->approver_id = $this->approvers_id;
                $ris->save();

                $log_entry = [
                    'For Release',
                    'Request For Release',
                    $old_value,
                    $ris,
                ];
                AC::logEntry($log_entry);

                session()->flash('success', 'The Item(s) Requested Is Now For Release!');
            } else {
                session()->flash('failed', 'For Release not found in the Approvals Module.');
            }

    /**
     * Receives a value to send and performs the necessary actions.
     *
     * @param mixed $value_to_send The value to be sent.
     * @throws ValidationException If a validation error occurs.
     * @return RedirectResponse The redirect response to the approval list.
     */
        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed!');
        } catch (\Exception $e) {
            session()->flash('failed', 'An error occurred while processing the release request.');
        }
        return redirect('/for/approval/list');
    }

    /**
     * Handles the received request.
     *
     * @param mixed $value_to_send The value to send to the function.
     * @throws ValidationException If a validation error occurs.
     * @throws \Exception If an error occurs while processing the received request.
     * @return RedirectResponse The redirect response after processing the request.
     */
    public function received($value_to_send)
    {
        try {
            $ris = RI::findOrFail($value_to_send);
            $old_value = clone $ris;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ($this->testIfArrayHaveSameElements ?
                    ['Received', 'RECEIVED', 'received'] :
                    ['Item Partially Checked Out Are Received', 'ITEM PARTIALLY CHECKED OUT ARE RECEIVED', 'item partially checked out are received']
                ))->first();

            if ($approval) {
                $ris->approval_id = $approval->id;
                $ris->approver_id = $this->approvers_id;
                $ris->active_status = 0;
                $ris->save();

                $farm_item_requested  = FIT::where('active_status', 1)->where('request_id', $ris->id)->get();

                foreach ($farm_item_requested as $key => $farm_items) {
                    // Farm Item exists in the database
                    $farm_item                                    = FIT::findorfail($farm_items->id);
                    $farm_item_old                                = $farm_item;
                    $oldQuantity                                  = (int) $farm_item->quantity;
                    $farm_item->quantity                          = $oldQuantity + $farm_item->item_quantity_just_checked_out;
                    $farm_item->item_quantity_just_checked_out    = 0;
                    $farm_item->request_id                        = $ris->id;
                    $farm_item->current_quantity                  = 0;
                    $farm_item->remarks                           = "N/A";
                    $farm_item->reorder_threshold                 = 0;
                    $farm_item->save();

                    $log_entry                             = ['Update', 'Farm Item', $farm_item_old, $farm_item, ];
                    AC::logEntry($log_entry);

                    $transaction_type_his = [
                        "checkout - withdrawal",
                        "Checkout - Withdrawal",
                        "CHECKOUT - WITHDRAWAL",
                        "Out", "out", "OUT",
                    ];

                    $item_history_his  = new FIH();
                    $item_history_his->farm_item_id        = $farm_item->id;
                    $item_history_his->transaction_type_id = TT::whereIn('transaction_type', $transaction_type_his)->get()->first()->id;
                    $item_history_his->previous_quantity   = $oldQuantity;
                    $item_history_his->new_quantity        = $farm_item->quantity;
                    $item_history_his->change_date         = date('Y-m-d H:i:s');
                    $item_history_his->change_reason       = "Requested/Withdrawn";
                    $item_history_his->user_id             = Auth::id();
                    $item_history_his->active_status       = true;
                    $item_history_his->deleted_status      = false;
                    $item_history_his->save();
                }


                $log_entry = [
                    'Received',
                    'Request Received',
                    $old_value,
                    $ris,
                ];
                AC::logEntry($log_entry);

                session()->flash('success', 'The Item(s) Is Now Received!');
            } else {
                session()->flash('failed', 'Received not found in the Approvals Module.');
            }

            // return redirect('/for/approval/list');
        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed!');
            // return redirect('/for/approval/list');
        } catch (\Exception $e) {
            session()->flash('failed', 'An error occurred while processing the received request.');
        }
        return redirect('/for/approval/list');
    }


    /**
     * Renders the view for the 'for-approval-list' component.
     *
     * @return View
     */
    public function render()
    {
        return view('livewire.for-approval.for-approval-list');
    }
}


