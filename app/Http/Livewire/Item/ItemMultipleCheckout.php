<?php

namespace App\Http\Livewire\Item;

use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use App\Models\WithdrawalSeries as WS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\SlackController    as SLCK;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;
use App\Models\ItemList as IL;
use App\Models\Item as IT;
use App\Models\FarmInventory as FIT;
use App\Models\InventoryHistory as IH;
use App\Models\UnitOfMeasurement as UOM;
use App\Models\FarmItemHistory as FIH;
use App\Models\Approvals;
use App\Models\TransactionType as TT;
use App\Models\Transaction;
use App\Models\User;
use App\Models\RequestItem as RI;
use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\ItemName as ITNAME;
use App\Models\Location as LC;
use App\Models\Supplier as SPL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Validation\ValidationException;

class ItemMultipleCheckout extends Component
{
    public $notes;
    public $farm_loc;
    public $dept_div;
    public $rem_quantity;
    public $json_value = [];
    public $selectedItems;

    public $item_id;
    public $item_name;
    public $category;
    public $subcategory;
    public $product;

    public $users_id;
    public $location_id;
    public $quantity;

    public $file_name;
    public $successfully_generated_message;
    public $req_id = 0;
    public $req_qty = null;
    public $arrayqty;
    public $requestid = 0;
    public $user_list;
    public $json_items;
    public $checkout_type;
    public $requested_items;
    public $rqstd_itms;


    /**
     * Clean and decode a string.
     *
     * @param string $string The string to be cleaned and decoded.
     * @return array The decoded array.
     */
    public function cleanAndDecode($string)
    {
        // Remove any non-numeric characters from the string
        $cleanedString = preg_replace('/[^0-9,]/', '', $string);

        // Remove trailing comma if present
        $cleanedString = rtrim($cleanedString, ',');

        // Convert the cleaned string to an array
        return json_decode("[$cleanedString]", true, 512, JSON_NUMERIC_CHECK);
    }

    /**
     * Mounts the given IDs and sets the request ID, request quantity, checkout type, and selected items.
     *
     * @param mixed $ids The IDs to be mounted.
     * @param int $reqid The request ID.
     * @param mixed|null $reqqty The request quantity.
     * @param int $request_id The request ID.
     * @param mixed|null $checkoutType The checkout type.
     * @return void
     */
    public function mount($ids, $reqid = 0, $reqqty = null, $request_id = 0, $checkoutType = null)
    {
        $this->req_id = $reqid;
        $this->req_qty = $reqqty;
        $this->requestid = $request_id;
        $this->checkout_type = $checkoutType;

        if($this->requestid != 0) {
            $this->requested_items = IL::where('request_item_id', $this->requestid)->get();

            foreach ($this->requested_items as $item) {
                $this->rqstd_itms[] = $item->item_released_quantity;
            }
        }

        if ($this->req_qty != null) {
            $this->arrayqty = $this->cleanAndDecode($this->req_qty);
        }

        $this->json_items = $this->cleanAndDecode($ids);

        $ctr = 0;
        foreach ($this->json_items as $id) {
            $item = IT::findorfail($id);
            $this->selectedItems[] = [
                'item_id' => $id,
                'item_image' => '
                    <img src="' . asset('photos/' . $item->item_image) . '" alt="Click to zoom" class="img-thumbnail" data-toggle="modal" data-target="#imageModal'.md5($item->id).'" style="width: 80px; height: 80px; max-width: 80px; max-height: 80px;">
                    <div class="modal fade" id="imageModal'.md5($item->id).'" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="imageModalLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="' . asset('photos/' . $item->item_image) . '" class="img-fluid" alt="Image" style="width: 80%; height: 80%; max-width: 80%; max-height: 80%;">
                                </div>
                            </div>
                        </div>
                    </div>
                ',
                'item_name'              => ITNAME::findorfail($item->item_name_id)->item_name,
                'item_category'          => CT::findorfail($item->category_id)->category_name,
                'item_subcategory'       => SCT::findorfail($item->subcategory_id)->subcategory_name,
                'item_product'           => PRD::findorfail($item->product_id)->product_name,
                'item_remaining_quantity' => $item->quantity,
                'item_selected_quantity'  => $this->checkout_type == 'full' ? $this->arrayqty[$ctr] - $this->rqstd_itms[$ctr] : 0,
            ];
            $ctr++;
        }

        $this->item_name = '';
        $this->rem_quantity = '';
        $this->item_id = $this->json_items[0];

        $users = User::with('access')->get();
        $this->user_list = [];
        $ctr = 1;
        foreach ($users as $user) {
            $this->user_list[] = [
                'num' => $ctr,
                'id' => $user->id . "," . FarmLocation::findorfail($user->farm_location_id)->farm_location . "," . DepartmentDivision::findorfail($user->department_division_id)->department_division,
                'full_name' => $user->name,
                'reqid' => $user->id,
            ];
            $ctr++;
        }
    }

    /**
     * Generate a QR code for the given item IDs.
     *
     * @param array $item_ids The array of item IDs.
     * @throws Some_Exception_Class A description of the exception that can be thrown.
     * @return void
     */
    public function generateQRCode($item_ids)
    {
        // Generate the QR code
        $qr_details = config('app.qr_url') . 'farmitem/details/' . Crypt::encryptString($item_ids);
        $qrCode = QrCode::size('500')->format('png')->generate($qr_details);

        // Save the QR code as a public file
        $this->file_name = md5($qr_details) . '.png';
        $path = public_path('farmqrcodes/' . $this->file_name);

        file_put_contents($path, $qrCode);

        $this->successfully_generated_message = "Farm Inventory QR Code Generated and Saved Successfully";
    }

    protected $listeners = ['createNewRecord'];

    /**
     * Creates a new record for a user.
     *
     * @param int $user_id The ID of the user.
     * @return void
     */
    public function createNewRecord($user_id)
    {
        // test if validator fails
        try{
            $transaction_type = [
                ($this->requestid != 0 ? "checkout - withdrawal" : "checkout - issuance"),
                ($this->requestid != 0 ? "Checkout - Withdrawal" : "Checkout - Issuance"),
                ($this->requestid != 0 ? "CHECKOUT - WITHDRAWAL" : "CKECKOUT - ISSUANCE"),
                "Out", "out", "OUT",
            ];

            $alternativePhrases = [
                "Item successfully checked out.",
                "Transaction successful.",
                "Checkout completed.",
                "Item Checked out successfully.",
                "Checkout successful.",
                "Transaction completed.",
                "Checked out successfully.",
                "Check Out Success.",
                "Item successfully acquired.",
                "Transaction confirmed.",
            ];

            foreach ($alternativePhrases as &$phrase) {
                $phrase = ucwords($phrase);
            }

            $randomPhrase = $alternativePhrases[array_rand($alternativePhrases)];

            $slck_title = ':arrow_up::paperclip: ' . $randomPhrase . '!';
            $slck_content = '';

            $slck_table = [
                ":arrow_up::paperclip: " . $randomPhrase . "!\n\n",
            ];

            if ($this->requestid != 0) {
                $ri = RI::findorfail($this->requestid);

                $ri->checkout_status = 1;
                $ri->comment .= $this->notes . ' ' . date('Y-m-d H:i:s') . '\.../';

                $approval = Approvals::where('active_status', 1)
                    ->whereIn('title', ($this->checkout_type == "partial" ?
                        ['Item Partially Checked Out', 'ITEM PARTIALLY CHECKED OUT', 'item partially checked out'] :
                        ['Item Fully Checked Out', 'ITEM FULLY CHECKED OUT', 'item fully checked out'])
                    )->first();
                $check_type = $this->checkout_type == "partial" ? "Partially" : "Fully";

                if ($approval) {
                    $ri->approval_id = $approval->id;
                    $ri->save();
                } else {
                    session()->flash('failed', '"Item ' . $check_type . ' Checked Out" Not Yet Set In The Approvals Module.');
                    return redirect('/for/approval/list');
                }
            }

            // For Transaction
            $sel_quants = [];
            foreach ($this->selectedItems as $key_title => $item) {
                $transac                          = new Transaction();
                $transac->item_id                 = $item['item_id'];
                $transac->assigned_by_user_id     = Auth::id();
                $transac->assigned_user_id        = $user_id;
                $transac->transaction_type_id     = TT::whereIn('transaction_type', $transaction_type)->get()->first()->id;
                $transac->farm_location_id        = User::findorfail($user_id)->farm_location_id;
                $transac->department_division_id  = User::findorfail($user_id)->department_division_id;
                $transac->quantity                = $item['item_selected_quantity'];
                $transac->transaction_date        = date('Y-m-d H:i:s');
                $transac->notes                   = $this->notes;
                $transac->active_status           = true;
                $transac->deleted_status          = false;
                $transac->save();

                $sel_quants[] = $item['item_selected_quantity'];

                $log_entry                        = ['Checkout', 'Transaction', '', json_encode($transac), ];
                AC::logEntry($log_entry);

                // For Item
                $item_mod                             = IT::findorfail($item['item_id']);
                $item_old = $item_mod;

                $previous_quantity                    = $item_old->quantity;
                // $previous_unit_price                  = ($item_old->purchase_cost / $item_old->quantity) ?? 0;
                if ($item_old->quantity != 0) {
                    $previous_unit_price = $item_old->purchase_cost / $item_old->quantity;
                } else {
                    $previous_unit_price = 0;
                }
                $previous_purchase                    = $item_old->purchase_date;
                $previous_expiry                      = $item_old->expiry_date;

                $item_mod->quantity                   = $previous_quantity - $item['item_selected_quantity'];
                // $item_mod->current_quantity           = 0;
                $item_mod->save();

                $log_entry                         = ['Update', 'Item', '', json_encode($item), ];
                AC::logEntry($log_entry);

                // For Farm Level
                $farmitems = FIT::where('item_id', $item['item_id'])->first(); // Retrieve user by email
                if ($farmitems)
                {
                    // Farm Item exists in the database
                    $farm_item                                    = FIT::findorfail($farmitems->id);
                    $farm_item_old                                = $farm_item;
                    $oldQuantity                                  = (int) $farm_item->quantity;
                    $farm_item->quantity                          = $this->requestid != 0 ? $oldQuantity : $oldQuantity + $item['item_selected_quantity'];

                    if ($this->requestid != 0) {
                        $farm_item->item_quantity_just_checked_out    += $item['item_selected_quantity'];
                        $farm_item->request_id                        = $this->requestid;
                    }

                    $farm_item->current_quantity                  = 0;
                    $farm_item->remarks                           = "N/A";
                    $farm_item->reorder_threshold                 = 0;
                    $farm_item->save();

                    $log_entry                             = ['Update', 'Farm Item', $farm_item_old, $farm_item, ];
                    AC::logEntry($log_entry);


                    if ($this->requestid < 1)
                    {
                        $transaction_type_his = [
                            "checkout - issuance",
                            "Checkout - Issuance",
                            "CHECKOUT - ISSUANCE",
                            "Out", "out", "OUT",
                        ];

                        $item_history_his  = new FIH();
                        $item_history_his->farm_item_id        = $farm_item->id;
                        $item_history_his->transaction_type_id = TT::whereIn('transaction_type', $transaction_type_his)->get()->first()->id;
                        $item_history_his->previous_quantity   = $oldQuantity;
                        $item_history_his->new_quantity        = $farm_item->quantity;
                        $item_history_his->change_date         = date('Y-m-d H:i:s');
                        $item_history_his->change_reason       = $this->requestid != 0 ? "Requested/Withdrawn" : "Issued";
                        $item_history_his->user_id             = Auth::id();
                        $item_history_his->active_status       = true;
                        $item_history_his->deleted_status      = false;
                        $item_history_his->save();
                    }


                } else
                {
                    // Farm Item doesn't exist
                    $farm_item                             = new FIT();

                    $farm_item->item_id                    = $item['item_id'];
                    $farm_item->user_assigned_id           = $user_id;
                    $farm_item->quantity                   = $this->requestid != 0 ? 0 : $item['item_selected_quantity'];

                    if ($this->requestid != 0) {
                        $farm_item->item_quantity_just_checked_out    += $item['item_selected_quantity'];
                        $farm_item->request_id                        = $this->requestid;
                    }

                    $farm_item->current_quantity           = 0;
                    $farm_item->remarks = "N/A";
                    $farm_item->reorder_threshold          = 0;
                    $farm_item->active_status              = true;
                    $farm_item->deleted_status             = false;
                    $farm_item->qr_code                    = "sample.png";
                    $farm_item->save();

                    $farm_items                             = FIT::findorfail($farm_item->id);
                    $this->generateQRCode($farm_items->id);
                    $farm_items->qr_code                    = $this->file_name;
                    $farm_items->save();

                    $log_entry                              = ['Create', 'Farm Item', '', $farm_item, ];
                    AC::logEntry($log_entry);

                    if ($this->requestid < 1)
                    {
                        $transaction_type_his = [
                            "checkout - issuance",
                            "Checkout - Issuance",
                            "CHECKOUT - ISSUANCE",
                            "Out", "out", "OUT",
                        ];

                        $item_history_his  = new FIH();
                        $item_history_his->farm_item_id        = $farm_items->id;
                        $item_history_his->transaction_type_id = TT::whereIn('transaction_type', $transaction_type_his)->get()->first()->id;
                        $item_history_his->previous_quantity   = 0;
                        $item_history_his->new_quantity        = $item['item_selected_quantity'];
                        $item_history_his->change_date         = date('Y-m-d H:i:s');
                        $item_history_his->change_reason       = $this->requestid != 0 ? "Requested/Withdrawn" : "Issued";
                        $item_history_his->user_id             = Auth::id();
                        $item_history_his->active_status       = true;
                        $item_history_his->deleted_status      = false;
                        $item_history_his->save();
                    }
                }

                // For Item History
                $item_history = new IH();
                $item_history->item_id             = $item['item_id'];
                $item_history->transaction_type_id = TT::whereIn('transaction_type', $transaction_type)->get()->first()->id;

                $item_history->previous_quantity   = $previous_quantity;
                $item_history->new_quantity        = $item_mod->quantity;

                $item_history->old_unit_price      = 0;
                $item_history->new_unit_price      = 0;

                $item_history->old_purchase_date   = $previous_purchase;
                $item_history->new_purchase_date   = $item_mod->purchase_date;

                $item_history->old_expiry_date     = $previous_expiry;
                $item_history->new_expiry_date     = $item_mod->expiry_date;


                $item_history->change_date         = date('Y-m-d H:i:s');
                $item_history->user_id             = Auth::id();
                $item_history->active_status       = true;
                $item_history->deleted_status      = false;
                $item_history->save();

                $log_entry                       = ['Create', 'Inventory History', '', json_encode($item_history), ];
                AC::logEntry($log_entry);

                $tempArray = [
                    "Category: " . (CT::findOrFail($item_mod->category_id)->category_name ?? 'N/A'),
                    "Sub Category: " . (SCT::findOrFail($item_mod->subcategory_id)->subcategory_name ?? 'N/A'),
                    "Product: " . (PRD::findOrFail($item_mod->product_id)->product_name ?? 'N/A'),
                    "Item: " . (ITNAME::findOrFail($item_mod->item_name_id)->item_name ?? 'N/A'),
                    "Model: " . ($item_mod->model_number ?? 'N/A'),
                    "Item Card: " . ($item_mod->item_number ?? 'N/A'),
                    "U/M: " . (UOM::findOrFail($item_mod->uom_id)->terminology ?? 'N/A'),
                    "Quantity Checked Out: " . ($item_mod->quantity ?? 'N/A') . "\n\n",
                ];

                array_push($slck_table, ...$tempArray);
            }

            if($this->requestid != 0) {
                $this->requested_items = IL::where('request_item_id', $this->requestid)->get();
                $ctr = 0;
                foreach ($this->requested_items as $item) {
                    $item->item_released_quantity += $sel_quants[$ctr];
                    $item->item_partially_release_quantity = $sel_quants[$ctr];
                    $item->save();
                    $ctr++;
                }
            }

            $tempArray = [
                "\n\nBy: " . (User::findOrFail(Auth::id())->name ?? 'N/A'),
                "To: " . (User::findOrFail($user_id)->name ?? 'N/A'),
            ];
            array_push($slck_table, ...$tempArray);
            $response = SLCK::sendSlackMessage($randomPhrase, $slck_title, $slck_content, $slck_table);

            if ($this->requestid != 0) {
                session()->flash('success', 'The Item(s) Requested Has Been Successfully Checked Out!');
                return redirect('/for/approval/list');
            }

            session()->flash('success', 'Items has been Checked Out!');

            return redirect('/item/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Checked Out Item!');
            return redirect('/item/list');
        }

    }

    public function render()
    {
        return view('livewire.item.item-multiple-checkout');
    }
}
