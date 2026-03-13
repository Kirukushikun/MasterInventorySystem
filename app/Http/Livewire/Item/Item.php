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
use App\Models\User;
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

class Item extends Component
{
    use WithFileUploads;

    public
        $item_image,
        $item_id_img,
        $item_id,
        $is_editting = false,
        $item_name,
        $category_id,
        $subcategory_id,
        $product_id,
        $item_name_id,
        $location_id,
        $supplier_id,
        $model_number,
        $item_number,
        $order_number,
        $uom_id,
        $quantity,
        $quantity_to_add,
        $reorder_threshold,
        $purchase_date,
        $expiry_date,
        $purchase_cost,
        $remarks,
        $user_id,
        $category_list,
        $location_list,
        $supplier_list,
        $uom_list,
        $file_name,
        $successfully_generated_message,
        $func_add_list = [],
        $for_inject_list = [],
        $listeners = [],
        $unit_price = 0,
        $temp_quantity = 0,
        $temp_cost = 0;

    public function rules()
    {
        return [
            'category_id'       => 'required|integer|exists:categories,id',
            'subcategory_id'    => 'required|integer|exists :sub_categories,id',
            'product_id'        => 'required|integer|exists:products,id',
            'item_name_id'      => [
                'required',
                'integer',
                'min:0',
                'exists:item_names,id',
            ],
            'location_id'       => 'required|integer|exists:locations,id',
            'model_number'      => 'nullable|string|max:255',
            'item_number'       => 'nullable|string|max:255',
            'supplier_id'       => 'nullable|integer|exists:suppliers,id',
            'uom_id'            => 'required|integer|exists:unit_of_measurements,id',
            'quantity'          => 'required|integer',
            'quantity_to_add'   => 'required|integer|regex:/^[+-]?\d+$/',
            'purchase_date'     => 'required|date',
            'expiry_date'       => 'nullable|date|after_or_equal:purchase_date',
            'purchase_cost'     => 'nullable|numeric|min:0',
            'unit_price'        => 'nullable|numeric|min:0',
            'remarks'           => 'nullable|string|max:1000',
            'item_image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }

    public function messages()
    {
        return [
            'additional_quantity.regex'       => 'Only Numerical Character Accepted',
            'item_image.image'                => 'Please Select a Valid Image',
            'item_image.mimes'                => 'Uploaded File Format Must Be: jpeg,png,jpg,gif,svg',
        ];
    }

    public function mount()
    {
        $this->category_id = null;
        $this->subcategory_id = null;

        $this->listeners            = ['createNewRecord'];
        $this->user_id              = Auth::id();
        $this->bin_location_list    = LC::where('active_status', 1)->get();
        $this->supplier_list        = SPL::where('active_status', 1)->get();
        $this->uom_list             = UOM::where('active_status', 1)->get();

        $this->category_list = CT::where('active_status', 1)->get();
        $this->subcategory_list = null;
        $this->product_list = null;
        $this->item_name_list = null;

        $this->quantity = 0;
        $this->quantity_to_add = " ";

        $this->func_add_list = [
            'Category' => 'category.div',
            'SubCategory' => 'subcategory.div',
            'Product' => 'product.div',
            'ItemName' => 'itemname.div',
            'BinLocation' => 'location.div',
            'Supplier' => 'supplier.div',
            'Uom' => 'uom.div'
        ];

        $this->for_inject_list = [
            'App\Models\Category' => 'ct',
            'App\Models\SubCategory' => 'sct',
            'App\Models\Product' => 'prd',
            'App\Models\ItemName' => 'itn',
            'App\Models\Location' => 'bl',
            'App\Models\Supplier' => 'sp',
            'App\Models\UnitOfMeasurement' => 'uom'
        ];

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

    public function find_item_data()
    {
        $item = IT::where('active_status', 1)
                    ->where('item_name_id', $this->item_name_id)->get()->first();

        $this->item_id            = ($item ? $item->quantity : 0) ;
        $this->quantity           = ($item ? $item->quantity : 0) ;
        $this->location_id        = ($item ? LC::findorfail($item->location_id)->id : null) ;
        $this->model_number       = ($item ? strtoupper($item->model_number) : null) ;
        $this->item_number        = ($item ? strtoupper($item->item_number) : null) ;
        $this->supplier_id        = ($item ? $item->supplier_id : null) ;
        $this->uom_id             = ($item ? UOM::findorfail($item->uom_id)->id : null) ;
        $this->purchase_date      = ($item ? $item->purchase_date : null) ;
        $this->expiry_date        = ($item ? $item->expiry_date : null) ;
        $this->purchase_cost      = ($item ? $item->purchase_cost : null) ;
        $this->unit_price         = ($item ? ($item->purchase_cost / ($item->current_quantity == 0 ? 1 : $item->current_quantity)) : null) ;
        $this->remarks            = ($item ? ($item->remarks ?? 'N/A') : null);

        if ($item){
            $this->is_editting = true;
            $this->item_id_img = asset('photos/' . $item->item_image);
            $this->reset('item_image');
            $this->item_image = null;
        }
        else{
            $this->is_editting = false;
        }

        $this->temp_cost = $this->purchase_cost;
        $this->temp_quantity = $this->quantity;
    }

    public function add_to_quantity()
    {

        if($this->quantity_to_add == 0 || $this->quantity_to_add == null || empty($this->quantity_to_add)){
            $this->quantity = $this->temp_quantity;
        }
        else{
            $quantity = $this->temp_quantity + $this->quantity_to_add;
            $this->quantity = $quantity;

            if ($quantity < 0) {
                $this->quantity = $this->temp_quantity;
                $this->quantity_to_add = 0;
            }


            if($this->unit_price == 0 || $this->unit_price == null || empty($this->unit_price)){
                $this->purchase_cost = $this->temp_cost;
            }
            else
            {
                $this->purchase_cost = ((int) $this->quantity_to_add * (int) $this->unit_price);
            }
        }


    }

    public function add_total_cost()
    {
        if($this->unit_price == 0 || $this->unit_price == null || empty($this->unit_price)){
            $this->purchase_cost = $this->temp_cost;
        }
        else
        {
            $this->purchase_cost = ((int) $this->quantity_to_add * (int) $this->unit_price);
        }
    }

    public function generate_qr_code($item_ids)
    {
        // Generate the QR code
        $qr_details = config('app.qr_url') . 'item/details/' . Crypt::encryptString($item_ids);
        $qrCode = QrCode::size('500')->format('png')->generate($qr_details);

        // Save the QR code as a public file
        'sampleQR.png';// = md5($qr_details) . '.png';
        $path = public_path('qrcodes/' . $this->file_name);

        file_put_contents($path, $qrCode);

        $this->successfully_generated_message = "QR Code Generated and Saved Successfully";
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



    public function createNewRecord()
    {
        // test if validator fails
        try{
            $this->validate();
            $item = IT::where('active_status', 1)
                    ->where('item_name_id', $this->item_name_id)->get()->first();

            if ($item) {
                // code...
                $item_old                         = $item;
                $previous_quantity                = $item->quantity;
                $previous_unit_price              = $item->purchase_cost;
                $previous_purchase                = $item->purchase_date;
                $previous_expiry                  = $item->expiry_date;
                $item->location_id                = $this->location_id;
                $item->model_number               = $this->model_number;
                $item->item_number                = $this->item_number;
                $item->supplier_id                = $this->supplier_id;
                $item->uom_id                     = $this->uom_id;
                $item->quantity                   = $this->quantity;
                $item->current_quantity           = $this->quantity_to_add;
                $item->purchase_date              = $this->purchase_date;
                $item->expiry_date                = $this->expiry_date;
                $item->purchase_cost              = $this->purchase_cost;
                $item->remarks                    = $this->remarks;
                $item->user_id                    = $this->user_id;
                $item->quantity                   = $this->quantity;
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
                    'Renew',
                    'Item',
                    $item_old ,
                    $item,
                ];
                AC::logEntry($log_entry);

                $transaction_type = ["Renew", "renew", "RENEW"];
                $item_history  = new IH();
                $item_history->item_id             = $item->id;
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

                session()->flash('success', 'Item has been Renewed!');
            }
            else{
                $item  = new IT();
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
                $item->reorder_threshold          = 0.10 * $this->quantity;
                $item->purchase_date              = $this->purchase_date;
                $item->expiry_date                = $this->expiry_date;
                $item->purchase_cost              = $this->purchase_cost;
                $item->remarks                    = $this->remarks;
                $item->user_id                    = $this->user_id;
                $item->current_quantity           = $this->quantity;
                $item->qr_code                    = "sample.png";
                $item->item_image                 = "sample.png";
                $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['For Approval', 'FOR APPROVAL', 'for approval'])
                ->first();

                if ($approval) {
                    $item->approval_id = $approval->id;


                    $item->active_status = 1;
                    $item->deleted_status = 0;
                    $item->save();

                    $items  = IT::findorfail($item->id);
                    $this->generate_qr_code($item->id);
                    $path = $this->item_image->storeAs('photos', md5($item->id) . '.png');
                    $this->resizeImageToLessThan3MB($path, 3);
                    $this->clearTemporaryFiles();
                    $items->qr_code                   = 'sampleQR.png';//$this->file_name;
                    $items->item_image                = md5($item->id) . '.png';
                    $items->item_image_path           = json_encode($this->item_image);
                    $items->save();

                    // $log_entry = [
                    //     'Create',
                    //     'Item',
                    //     '',
                    //     $item,
                    // ];

                    // AC::logEntry($log_entry);

                    // $transaction_type = ["CREATE", "ADD", "Add", "Create", "add", "create"];
                    // $item_history  = new IH();
                    // $item_history->item_id             = $item->id;
                    // $item_history->transaction_type_id = TT::whereIn('transaction_type', $transaction_type)->get()->first()->id;
                    // $item_history->new_quantity        = $this->quantity;
                    // $item_history->new_unit_price      = $this->unit_price;
                    // $item_history->new_purchase_date   = $this->purchase_date;
                    // $item_history->new_expiry_date     = $this->expiry_date;
                    // $item_history->change_date         = date('Y-m-d H:i:s');
                    // $item_history->user_id             = $this->user_id;
                    // $item_history->active_status       = true;
                    // $item_history->deleted_status      = false;
                    // $item_history->save();
                    // session()->flash('success', 'New Item has been Created!, \n' . $this->successfully_generated_message . '!');

                    session()->flash('success', 'Successfull, The Recently Created Item Is In Need of Approval!');
                    return redirect('/item/list');
                }
                else{
                    session()->flash('success', 'For Approval not found in the Approvals Module.');
                    return redirect('/item/list');
                }

            }
            return redirect('/item/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Item!');
            return redirect('/item/list');
        }

    }

    public function clearTemporaryFiles()
    {
        $temporaryFilesDirectory = storage_path('app/livewire-tmp');

        // Use the File class to delete the files
        File::cleanDirectory($temporaryFilesDirectory);
    }

    public function clear_fields()
    {
        $this->category_id = null;
        $this->subcategory_id = null;
        $this->product_id = null;
        $this->item_name_id = null;
        $this->location_id = null;
        $this->supplier_id = null;
        $this->mount();
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

    public function render()
    {
        return view('livewire.item.item');
    }

    //okay
}
