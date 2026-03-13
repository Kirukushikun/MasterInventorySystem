<?php

namespace App\Http\Livewire\Item;

use Livewire\Component;

use Auth;
use App\Models\WithdrawalSeries             as WS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController    as AC;
use App\Http\Controllers\GeneralController  as GC;
use App\Http\Controllers\SlackController    as SLCK;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;
use App\Models\ItemList                     as IL;
use App\Models\Item                         as IT;
use App\Models\InventoryHistory             as IH;
use App\Models\UnitOfMeasurement            as UOM;
use App\Models\Approvals;
use App\Models\TransactionType              as TT;
use App\Models\UsedSeries                   as US;
use App\Models\RequestItem                  as RI;
use App\Models\Category                     as CT;
use App\Models\Location                     as LC;
use App\Models\Supplier                     as SPL;
use App\Models\SubCategory                  as SCT;
use App\Models\Product                      as PRD;
use App\Models\ItemName                     as ITNAME;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AccessController as ACC;

class ItemUpdate extends Component
{
    use WithFileUploads;


    public $item_name,
        $item_image,
        $item_id_img,
        $is_editting = true;
    public $category_id ;
    public $subcategory_id;
    public $product_id;
    public $item_name_id;
    public $location_id;

    public $model_number;
    public $item_number;
    public $order_number;
    public $supplier_id ;
    public $uom_id;
    public $quantity;
    public $additional_quantity;
    public $reorder_threshold;
    public $purchase_date;
    public $expiry_date;
    public $purchase_cost;
    public $remarks;

    public $user_id;
    public $category_list;
    public $location_list;
    public $supplier_list;
    public $uom_list;
    public $file_name;
    public $qr_code;
    public $successfully_generated_message;

    public $listeners = [];

    public $items_id;
    public $temp_quantity;
    public $temp_cost;
    public $unit_price = 0;
    public $temp_curr;
    public $temp_purcost;

    public function rules()
    {
        $all_data = IT::where('active_status', 1)->where('id', '!=', $this->items_id)->get();
        return [
            'category_id'       => 'required|integer|exists:categories,id',
            'subcategory_id'    => 'required|integer|exists:sub_categories,id',
            'product_id'        => 'required|integer|exists:products,id',
            'item_name_id'      => [
                'required',
                'integer',
                'min:0',
                'exists:item_names,id',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->item_name_id) == strtolower($value)) {
                            $fail("This Inventory Item Already Exist");
                            break;
                        }
                    }
                },
            ],
            'location_id'       => 'required|integer|exists:locations,id',
            'model_number'      => 'nullable|string|max:255',
            'item_number'       => 'nullable|string|max:255',
            'supplier_id'       => 'nullable|integer|exists:suppliers,id',
            'uom_id'            => 'required|integer|exists:unit_of_measurements,id',
            'quantity'          => 'required|integer',
            'additional_quantity'=> 'nullable|numeric|regex:/^[+-]?\d+$/',
            'purchase_date'     => 'required|date',
            'expiry_date'       => 'nullable|date|after_or_equal:purchase_date',
            'purchase_cost'     => 'nullable|numeric',
            'unit_price'        => 'nullable|numeric|min:0',
            'remarks'           => 'nullable|string|max:1000',
            'item_image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }

    public function messages()
    {
        return [
            'item_name_id.unique'       => 'This Item Already Exist in Inventory',
            'additional_quantity.regex'       => 'Only Numerical Character Accepted',
            'item_image.image'                => 'Please Select a Valid Image',
            'item_image.mimes'                => 'Uploaded File Format Must Be: jpeg,png,jpg,gif,svg',
        ];
    }


    public function mount($id)
    {
        $this->listeners        = ['editRecord'];
        $this->user_id          = Auth::id();
        $this->category_list    = CT::where('active_status', 1)->get();
        $this->bin_location_list    = LC::where('active_status', 1)->get();
        $this->supplier_list    = SPL::where('active_status', 1)->get();
        $this->uom_list         = UOM::where('active_status', 1)->get();
        $this->items_id = $id;

        $this->subcategory_list = SCT::where('active_status', 1)->get();
        $this->product_list = PRD::where('active_status', 1)->get();
        $this->item_name_list = ITNAME::where('active_status', 1)->get();

        $item = IT::findorfail($id);

        $this->category_id = CT::findorfail($item->category_id)->id;
        $this->subcategory_id = SCT::findorfail($item->subcategory_id)->id;
        $this->product_id = PRD::findorfail($item->product_id)->id;
        $this->item_name_id = ITNAME::findorfail($item->item_name_id)->id;


        $this->location_id = LC::findorfail($item->location_id)->id;
        $this->model_number = strtoupper($item->model_number);
        $this->item_number = strtoupper($item->item_number);
        $this->supplier_id = $item->supplier_id;

        $this->uom_id = UOM::findorfail($item->uom_id)->id;
        $this->quantity = $item->quantity;
        $this->additional_quantity = "";

        $this->reorder_threshold = $item->reorder_threshold ?? 'N/A';
        $this->purchase_date = $item->purchase_date;
        $this->expiry_date = $item->expiry_date;
        $this->purchase_cost = $item->purchase_cost;
        $this->unit_price = $item->purchase_cost / ($item->current_quantity <= 0 ? 1 : $item->current_quantity);
        $this->remarks = $item->remarks ?? 'N/A';

        $this->qr_code = asset('qrcodes/' . $item->qr_code);

        $this->temp_quantity = $this->quantity;
        $this->temp_cost = $this->purchase_cost;
        $this->temp_curr = $item->current_quantity;

        if ($item){
            $this->is_editting = true;
            $this->item_id_img = asset('photos/' . $item->item_image);
            $this->reset('item_image');
            $this->item_image = null;
        }
        else{
            $this->is_editting = false;
        }


        $this->subcategory_list = SCT::where('category_id', $this->category_id)->where('active_status', 1)->get();

        $this->product_list = PRD::where('category_id', $this->category_id)
                                ->where('subcategory_id', $this->subcategory_id)->where('active_status', 1)
                                ->get();

        $this->item_name_list = ITNAME::where('category_id', $this->category_id)
                                    ->where('subcategory_id', $this->subcategory_id)
                                    ->where('product_id', $this->product_id)->where('active_status', 1)
                                    ->get();
    }

    public function add_to_quantity()
    {

        if(!ACC::checkAccess(Auth::id(), 'inventory_diminish')){
            if($this->additional_quantity == 0 || $this->additional_quantity == null || empty($this->additional_quantity)){
                $this->quantity = $this->temp_quantity;
            }
            else{
                $quantity = $this->temp_quantity + $this->additional_quantity;
                $this->quantity = $quantity;

                if ($quantity < 0) {
                    $this->quantity = $this->temp_quantity;
                    $this->additional_quantity = 0;
                }


                // if($this->unit_price == 0 || $this->unit_price == null || empty($this->unit_price)){
                //     $this->purchase_cost = $this->temp_cost;
                // }
                // else
                // {
                    $this->purchase_cost = ((int) $this->additional_quantity * (int) $this->unit_price);
                // }
            }
        }else{

            $this->purchase_cost = $this->temp_cost;

            if($this->additional_quantity == 0 || $this->additional_quantity == null || empty($this->additional_quantity)){
                $this->quantity = $this->temp_quantity;
                $this->purchase_cost = $this->temp_cost;
            }
            else{
                $quantity = $this->temp_quantity + $this->additional_quantity;
                $this->quantity = $quantity;

                if ($quantity < 0) {
                    $this->quantity = $this->temp_quantity;
                    $this->additional_quantity = 0;
                }

                if ($this->additional_quantity > 0)
                {
                    // $this->purchase_cost = ($this->additional_quantity + $this->temp_quantity) * $this->unit_price;
                    $this->purchase_cost =  $this->purchase_cost + ((int) $this->additional_quantity * (int) $this->unit_price);
                }
                else{
                    $this->purchase_cost = $this->purchase_cost + ((int) $this->additional_quantity * (int) $this->unit_price);
                }

            }
        }


    }

    public function add_total_cost()
    {

        if(!ACC::checkAccess(Auth::id(), 'inventory_diminish')){
            if($this->unit_price == 0 || $this->unit_price == null || empty($this->unit_price)){
                $this->purchase_cost = $this->temp_cost;
            }
            else
            {
                $this->purchase_cost = ((int) $this->additional_quantity * (int) $this->unit_price);
            }
        }else{
            if($this->additional_quantity == 0 || $this->additional_quantity == null || empty($this->additional_quantity))
            {
                if($this->unit_price == 0 || $this->unit_price == null || empty($this->unit_price)){
                    $this->purchase_cost = $this->temp_cost;
                }
                else
                {
                    $this->purchase_cost = ((int) $this->temp_curr * (int) $this->unit_price);
                    $this->temp_cost = $this->purchase_cost;
                }
            }else{
                if ($this->additional_quantity > 0) {
                    if($this->unit_price == 0 || $this->unit_price == null || empty($this->unit_price)){
                        $this->purchase_cost = $this->temp_cost;
                        $this->temp_cost = $this->purchase_cost;
                    }
                    else
                    {
                        $this->purchase_cost = ((int) ($this->temp_curr + $this->additional_quantity) * (int) $this->unit_price);
                        $this->temp_cost = $this->purchase_cost;
                    }
                }else{
                    $this->purchase_cost = ((int) ($this->temp_curr + $this->additional_quantity) * (int) $this->unit_price);
                    $this->temp_cost = $this->purchase_cost;
                }
            }
        }

    }

    public function set_subcategory()
    {
        $category_id = $this->category_id;
        $this->subcategory_list = SCT::where('category_id', $category_id)
                                    ->where('active_status', 1)->get();
    }

    public function set_product()
    {
        $category_id = $this->category_id;
        $subcategory_id = $this->subcategory_id;
        $this->product_list = PRD::where('category_id', $category_id)
                                ->where('subcategory_id', $subcategory_id)
                                ->where('active_status', 1)
                                ->get();
    }

    public function set_item()
    {
        $category_id = $this->category_id;
        $subcategory_id = $this->subcategory_id;
        $product_id = $this->product_id;
        $this->item_name_list = ITNAME::where('category_id', $category_id)
                                    ->where('subcategory_id', $subcategory_id)
                                    ->where('product_id', $product_id)
                                    ->where('active_status', 1)
                                    ->get();
    }

    public function generateQRCode($item_ids)
    {
        // Generate the QR code
        $qr_details = config('app.qr_url') . 'item/details/' . Crypt::encryptString($item_ids);
        $qrCode = QrCode::size('500')->format('png')->generate($qr_details);

        // Save the QR code as a public file
        $this->file_name = md5($qr_details) . '.png';
        $path = public_path('qrcodes/' . $this->file_name);

        file_put_contents($path, $qrCode);

        $this->successfully_generated_message = "QR Code Updated";
    }

    function resizeImageToLessThan3MB($path, $size)
    {
        $image = Image::make(storage_path("app/{$path}"));

        // Calculate the maximum file size (3MB)
        $maxFileSizeInBytes = $size * 1024 * 1024;

        // Initial quality
        $quality = 90;

        // Resize the image to 200x200 while maintaining aspect ratio
        // $image->fit(200, 200);

        while (strlen($image->encode('data-url', $quality)) > $maxFileSizeInBytes) {
            $quality -= 5;
        }

        $image->save(storage_path("app/{$path}"), $quality);
    }

    public function editRecord()
    {
        // test if validator fails
        try{
            $this->validate();
            $item  = IT::findorfail($this->items_id);
            $item_old  = $item;
            $previous_quantity                = $item->quantity;
            $previous_unit_price              = $item->purchase_cost;
            $previous_purchase                = $item->purchase_date;
            $previous_expiry                  = $item->expiry_date;
            $item->category_id                = $this->category_id;
            $item->subcategory_id             = $this->subcategory_id;
            $item->product_id                 = $this->product_id;
            $item->item_name_id               = $this->item_name_id;
            $item->location_id                = $this->location_id;
            $item->model_number               = $this->model_number;
            $item->item_number                = $this->item_number;
            $item->supplier_id                = $this->supplier_id;
            $item->uom_id                     = $this->uom_id;
            $item->quantity                   = $this->quantity;

            $item->current_quantity           = $item->current_quantity + ($this->additional_quantity == "" ? 0 : $this->additional_quantity);

            $item->purchase_date              = $this->purchase_date;
            $item->expiry_date                = $this->expiry_date;
            $item->purchase_cost              = $this->purchase_cost;
            $item->remarks                    = $this->remarks;
            $item->user_id                    = $this->user_id;
            $item->save();

            if(isset($this->item_image) || !empty($this->item_image) || !is_null($this->item_image) || $this->item_image != null){
                $path = $this->item_image->storeAs('photos', md5($item->id) . '.png');
                $this->resizeImageToLessThan3MB($path, 3);
                $this->clearTemporaryFiles();
                $item->item_image                = md5($item->id) . '.png';
                $item->item_image_path           = json_encode($this->item_image);
                $item->save();
            }

            $log_entry = [
                'Update',
                'Item',
                $item_old,
                $item,
            ];
            AC::logEntry($log_entry);

            // [ IN, OUT, CREATE, UPDATE, DELETE]

            $transaction_type = ["UPDATE", "EDIT", "Edit", "Update", "edit", "update"];
            $item_history  = new IH();
            $item_history->item_id             = $this->items_id;
            $item_history->transaction_type_id = TT::whereIn('transaction_type', $transaction_type)->get()->first()->id;

            $item_history->previous_quantity   = $previous_quantity;
            $item_history->new_quantity        = $this->quantity;

            $item_history->old_unit_price      = $previous_unit_price;
            $item_history->new_unit_price      = $this->unit_price;

            $item_history->old_purchase_date   = $previous_purchase;
            $item_history->new_purchase_date   = $this->purchase_date;

            $item_history->old_expiry_date     = $previous_expiry;
            $item_history->new_expiry_date     = $this->expiry_date;

            $item_history->change_date         = date('Y-m-d H:i:s');
            $item_history->user_id             = $this->user_id;

            $item_history->active_status       = true;
            $item_history->deleted_status      = false;
            $item_history->save();


            session()->flash('success', 'Item has been Updated! \n' . $this->successfully_generated_message);

            return redirect('/item/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Item!');
            return redirect('/item/list');
        }

    }

    public function clearTemporaryFiles()
    {
        $temporaryFilesDirectory = storage_path('app/livewire-tmp');

        // Use the File class to delete the files
        File::cleanDirectory($temporaryFilesDirectory);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.item.item-update');
    }
}
