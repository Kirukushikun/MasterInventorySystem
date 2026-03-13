<?php

namespace App\Http\Livewire\Item;

use Livewire\Component;

use Auth;
use App\Models\WithdrawalSeries as WS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AccessController as ACC;
use App\Http\Controllers\SlackController    as SLCK;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;
use App\Models\ItemList as IL;
use App\Models\Item as IT;
use App\Models\FarmInventory as FIT;
use App\Models\FarmItemHistory as FIH;
use App\Models\InventoryHistory as IH;
use App\Models\UnitOfMeasurement as UOM;
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
// use App\Notifications\SlackNotification;

class ItemCheckout extends Component
{
    // use Notifiable;

    public $item_id;
    public $item_name;
    public $category;
    public $subcategory;
    public $product;

    public $users_id;
    public $location_id;
    public $quantity;
    public $notes;

    public $file_name;
    public $successfully_generated_message;
    public $unit_price;
    public $rem_quantity;

    public function mount($id)
    {

        $item = IT::findorfail($id);
        $this->item_name = ITNAME::findorfail($item->item_name_id)->item_name;

        $this->category     = CT::findorfail($item->category_id)->category_name;
        $this->subcategory  = SCT::findorfail($item->subcategory_id)->subcategory_name;
        $this->product      = PRD::findorfail($item->product_id)->product_name;

        $this->rem_quantity = $item->quantity;
        $this->item_id = $id;

        $users = User::with('access')->get();
        $this->user_list = [];
        $ctr = 1;

        foreach ($users as $user) {
            $this->user_list[] = [
                'num' => $ctr,
                'id' => $user->id . "," . FarmLocation::findorfail($user->farm_location_id)->farm_location . "," . DepartmentDivision::findorfail($user->department_division_id)->department_division,
                'full_name' => strtoupper($user->name)
            ];
            $ctr++;
        }
        if (ACC::checkAccess(Auth::id(), 'inventory_diminish')) {
            $this->user_list[] = [
                'num' => $ctr,
                'id' => 0 . ",N/A,N/A",
                'full_name' => 'DIMINISH (USE ONLY WHEN NECESSARY)'
            ];
        }
        $this->unit_price         = $item->purchase_cost / $item->current_quantity;

    }

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

    public function createNewRecord($user_id)
    {
        try {
            $transaction_type = ["DIMINISH", "diminish", "Diminish"];

            if ($user_id != 0) {
                $transaction_type = ["checkout - issuance", "Checkout - Issuance", "CHECKOUT - ISSUANCE", "Out", "out", "OUT",];
            }

            $item = IT::findOrFail($this->item_id);

            $item_old = $item;
            $previous_quantity                = $item->quantity;
            $previous_unit_price              = $item->purchase_cost / $item->quantity;
            $previous_purchase                = $item->purchase_date;
            $previous_expiry                  = $item->expiry_date;
            // $item->purchase_cost              = ($item->purchase_cost / ($item->current_quantity - $this->quantity)) * ($item->current_quantity - $this->quantity);
            // $purchase_cost                    = $item->purchase_cost / ($item->current_quantity - $this->quantity);
            $item->quantity                   -= $this->quantity;
            // $item->current_quantity           -= $this->quantity;
            $item->save();

            $transaction = new Transaction();
            $transaction->item_id = $this->item_id;
            $transaction->assigned_by_user_id = Auth::id();
            $transaction->assigned_user_id = $user_id ?: Auth::id();
            $transaction->transaction_type_id = TT::whereIn('transaction_type', $transaction_type)->firstOrFail()->id;
            $transaction->farm_location_id = $user_id ? User::findOrFail($user_id)->farm_location_id : Auth::user()->farm_location_id;
            $transaction->department_division_id = $user_id ? User::findOrFail($user_id)->department_division_id : Auth::user()->department_division_id;
            $transaction->quantity = $this->quantity;
            $transaction->transaction_date = now();
            $transaction->notes = $this->notes;
            $transaction->active_status = $user_id != 0;
            $transaction->deleted_status = false;
            $transaction->save();

            $log_entry = [$user_id != 0 ? 'Checkout' : 'Diminished - Checkout', 'Transaction', $user_id != 0 ? '' : 'Diminish', $transaction];
            AC::logEntry($log_entry);

            if ($user_id != 0) {
                $farmItem = FIT::where('item_id', $this->item_id)->where('user_assigned_id', $user_id)->first();

                if ($farmItem) {
                    $farm_item_old = $farmItem;
                    $prev_quan = $farmItem->quantity;
                    $farmItem->quantity += $this->quantity;
                    $farmItem->current_quantity = 0;
                    $farmItem->remarks = "N/A";
                    $farmItem->reorder_threshold = 0;
                    $farmItem->save();

                    $log_entry = ['Update', 'Farm Item', $farm_item_old, $farmItem];
                    AC::logEntry($log_entry);

                    $transaction_type_his = ["checkout - issuance", "Checkout - Issuance", "CHECKOUT - ISSUANCE"];

                    $item_history_his  = new FIH();
                    $item_history_his->farm_item_id        = $farmItem->id;
                    $item_history_his->transaction_type_id = TT::whereIn('transaction_type', $transaction_type_his)->get()->first()->id;
                    $item_history_his->previous_quantity   = $prev_quan;
                    $item_history_his->new_quantity        = $farmItem->quantity;
                    $item_history_his->change_date         = date('Y-m-d H:i:s');
                    $item_history_his->change_reason       = "Issued";
                    $item_history_his->user_id             = Auth::id();
                    $item_history_his->active_status       = true;
                    $item_history_his->deleted_status      = false;
                    $item_history_his->save();

                } else {
                    $farmItem = new FIT();
                    $farmItem->item_id = $this->item_id;
                    $farmItem->user_assigned_id = $user_id;
                    $farmItem->quantity = $this->quantity;
                    $farmItem->current_quantity = 0;
                    $farmItem->reorder_threshold = 0;
                    $farmItem->active_status = true;
                    $farmItem->deleted_status = false;
                    $farmItem->qr_code = "sample.png";
                    $farmItem->remarks = "N/A";
                    $farmItem->save();
                    $this->generateQRCode($farmItem->id);
                    $farmItem->qr_code = $this->file_name;
                    $farmItem->save();
                    $log_entry = ['Create', 'Farm Item', '', $farmItem];
                    AC::logEntry($log_entry);

                    $transaction_type_his = ["checkout - issuance", "Checkout - Issuance", "CHECKOUT - ISSUANCE"];

                    $item_history_his  = new FIH();
                    $item_history_his->farm_item_id        = $farmItem->id;
                    $item_history_his->transaction_type_id = TT::whereIn('transaction_type', $transaction_type_his)->get()->first()->id;
                    $item_history_his->previous_quantity   = 0;
                    $item_history_his->new_quantity        = $this->quantity;
                    $item_history_his->change_date         = date('Y-m-d H:i:s');
                    $item_history_his->change_reason       = "Issued";
                    $item_history_his->user_id             = Auth::id();
                    $item_history_his->active_status       = true;
                    $item_history_his->deleted_status      = false;
                    $item_history_his->save();
                }
            }

            $item_history = new IH();
            $item_history->item_id = $this->item_id;
            $item_history->transaction_type_id = TT::whereIn('transaction_type', $transaction_type)->firstOrFail()->id;

            $item_history->previous_quantity   = $previous_quantity;
            $item_history->new_quantity        = $item->quantity;

            $item_history->old_unit_price      = 0;
            $item_history->new_unit_price      = 0;

            $item_history->old_purchase_date   = $previous_purchase;
            $item_history->new_purchase_date   = $item->purchase_date;

            $item_history->old_expiry_date     = $previous_expiry;
            $item_history->new_expiry_date     = $item->expiry_date;

            $item_history->change_date = now();
            $item_history->user_id = Auth::id();
            $item_history->active_status = true;
            $item_history->deleted_status = false;
            $item_history->save();

            $log_entry = ['Create', 'Inventory History', '', $item_history];
            AC::logEntry($log_entry);

            $alternativePhrases = [
                "Item successfully checked out.",
                "Transaction successful.",
                "Checkout completed.",
                "Item checked out successfully.",
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

            $tempArray = [
                "Category: " . (CT::findOrFail($item->category_id)->category_name ?? 'N/A'),
                "Sub Category: " . (SCT::findOrFail($item->subcategory_id)->subcategory_name ?? 'N/A'),
                "Product: " . (PRD::findOrFail($item->product_id)->product_name ?? 'N/A'),
                "Item: " . (ITNAME::findOrFail($item->item_name_id)->item_name ?? 'N/A'),
                "Model: " . ($item->model_number ?? 'N/A'),
                "Item Card: " . ($item->item_number ?? 'N/A'),
                "U/M: " . (UOM::findOrFail($item->uom_id)->terminology ?? 'N/A'),
                "Quantity Checked Out: " . ($this->quantity ?? 'N/A'),
            ];

            array_push($slck_table, ...$tempArray);

            $tempArray = [
                "\n\nBy: " . (User::findOrFail($item->user_id)->name ?? 'N/A'),
                "To: " . (User::findOrFail($user_id)->name ?? 'N/A'),
            ];

            array_push($slck_table, ...$tempArray);

            // $response = SLCK::sendSlackMessage($randomPhrase, $slck_title, $slck_content, $slck_table);

            session()->flash('success', 'Item has been ' . ($user_id != 0 ? 'Checked Out!' : 'Diminished!') . '\n' . $this->successfully_generated_message);
            return redirect('/item/list');

        } catch (ValidationException $e) {
            session()->flash('failed', 'Failed to ' . ($user_id != 0 ? 'Check Out' : 'Diminish') . ' Item!');
            return redirect('/item/list');
        }
    }

    public function render()
    {
        return view('livewire.item.item-checkout');
    }
}
