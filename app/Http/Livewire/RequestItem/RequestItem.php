<?php

namespace App\Http\Livewire\RequestItem;

use Livewire\Component;

use Auth;
use App\Models\WithdrawalSeries as WS;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\DepartmentDivision;
use App\Models\FarmLocation;
use App\Models\FarmInventory;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Item as IT;
use App\Models\Product;
use App\Models\ItemList as IL;
use App\Models\UnitOfMeasurement as UOM;
use App\Models\Approvals;
use App\Models\ItemName;
use App\Models\User;
use App\Models\UsedSeries as US;
use App\Models\RequestItem as RI;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RequestItem extends Component
{
    use WithFileUploads;

    public $series_number, $requested_by_id, $farm_location_id, $department_division_id, $approval_id, $remarks, $date_requested, $date_needed, $active_status, $deleted_status, $pdfName, $jl_pdf, $name, $randNum;
    public $items_requested =
    [
        [
            'item_id' => null,
            'item_image' => '
                <img src="https://upload.wikimedia.org/wikipedia/commons/1/14/Product_sample_icon_picture.png" alt="Click to zoom" class="img-thumbnail" data-toggle="modal" data-target="#imageModal" style="width: 80px; height: 80px; max-width: 80px; max-height: 80px;">
                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/1/14/Product_sample_icon_picture.png" class="img-fluid" alt="Image" style="width: 80%; height: 80%; max-width: 80%; max-height: 80%;">
                            </div>
                        </div>
                    </div>
                </div>
            ',
            'item_name' => null,
            'item_category' => null,
            'item_subcategory' => null,
            'item_product' => null,
            'uom_id' => null,
            'rem_quan' => null,
            'reorder' => null,
            'item_quantity' => null
        ]
    ];

    public $listeners = [], $farm_location_list, $department_division_list, $uom_list, $item_name_list, $sampel, $no_series_available = false,
        $available_farms = [],  // Array of farms that have all selected items
        $selected_farm = null,
        $central_warehouse_id = 1; // Change based on actual ID

    public $farms_with_supply = [];

    public function rules()
    {
        $rules = [
            'series_number' => 'required|string',
            'farm_location_id' => 'required|integer',
            'department_division_id' => 'required|integer',
            'date_requested' => 'required|string',
            'date_needed' => 'required|string',
            'jl_pdf' => 'required|file|mimes:pdf|max:5120',
        ];

        // Loop through each item in items_requested array and define rules

        foreach ($this->items_requested as $index => $item) {
            $rules["items_requested.$index.item_name"] = [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($index, $item) {
                    $duplicates = collect($this->items_requested)
                        ->where('item_name', $value)
                        ->count();

                    if ($duplicates > 1) {
                        $fail(strtoupper(ItemName::findorfail($value)->item_name) . " Is Already Picked");
                    }
                },
            ];
            $rules["items_requested.$index.item_quantity"] = [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($index, $item) {

                    $itemOnHandQty = $item['rem_quan'];

                    // Check if Qty inputted is more than Qty-on-hand
                    // if ($value > $itemOnHandQty) {
                    //     $fail("Qty inputted is more than Qty-on-hand.");
                    // }

                    // check if there are pending request that exceeds the inventory stock for this item
                    // $pendingRequests = IL::where('item_id', $item['item_id'])
                    //     ->where('active_status', 1)
                    //     ->where('deleted_status', 0)
                    //     ->whereHas('requestItem', function ($query) {
                    //         $query->where('approval_id', '!=', 7); // Exclude approved requests
                    //     })
                    //     ->sum('item_quantity');
                    // if ($pendingRequests + $value > $itemOnHandQty) {
                    //     $fail("Inputted Qty exceeds the available stock. There are Pending requests for this item");
                    // }

                    // check if the current main inventory(IL) stock can still handle the requested quantity minus the pending
                    $pendingRequests = IL::where('item_id', $item['item_id'])
                        ->where('active_status', 1)
                        ->where('deleted_status', 0)
                        ->whereHas('requestItem', function ($query) {
                            $query->where('approval_id', 5); // Exclude approved requests
                        })
                        ->sum('item_quantity');

                    if ($pendingRequests + $value > $itemOnHandQty) {
                        $fail("Inputted Qty exceeds the available stock. There are Pending requests for this item.");
                    }

                    // Check if Qty-On-Hand is equal or less than Inputted Qty
                    if ($itemOnHandQty == $value) {
                        $fail("Qty-On-Hand is equal Inputted Qty.");
                    }

                    // Check if Inputted Qty is less than or equal to Re-order Qty
                    $reorderQty = $item['reorder'];

                    // if ($item['reorder'] == 0 || $item['reorder'] == '' || $item['reorder'] == null) {
                    //     $fail("Reorder Quantity Is Not Set.");
                    // }

                    // if ($value > ($itemOnHandQty - $reorderQty)) {
                    //     $fail("Inputted Qty Will Affect The Re-order Qty, Please Input New Quantity.");
                    // }

                    if ($value == 0) {
                        $fail("Invalid Qty. Please Input A Quantity Greater than zero");
                    }
                },
            ];
        }

        return $rules;
    }

    public function updatedJlPdf()
    {
        $this->validate([
            'jl_pdf' => 'required|file|mimes:pdf|max:5120', // Adjust the max file size as needed
        ]);

        // Generate a unique name for the PDF file
        $this->pdfName = 'pdf-' . date('Y-m-d-H-i-s');
    }

    public function uploadPdf()
    {
        $this->validate([
            'jl_pdf' => 'file|mimes:pdf|max:5120', // Adjust the max file size as needed
        ]);

        // Rename the file with the generated unique name
        $this->jl_pdf->storeAs('pdfs', $this->pdfName . '.pdf');

    }

    public function messages()
    {
        return [
            'items_requested.*.item_name.required' => 'Item Name is required',
            'items_requested.*.item_quantity.required' => 'Quantity is required',
        ];
    }

    public function mount()
    {
        $user = User::with('farmLocation', 'departmentDivision')->findOrFail(Auth::id());
        $farm_location = $user->farmLocation;
        $dept_code = $user->departmentDivision->abbreviation;

        $this->name = $user->name;
        $this->farm_location_id = $farm_location->id;
        $this->department_division_id = $user->department_division_id;
        $this->series_number = $farm_location->abbreviation . "-" . $dept_code . "-";

        $this->listeners = ['createNewRecord'];
        $this->farm_location_list = FarmLocation::where('active_status', 1)->get();
        $this->department_division_list = DepartmentDivision::where('active_status', 1)->get();
        $this->uom_list = UOM::where('active_status', 1)->get();

        $item_names = IT::where('active_status', 1)->where('approval_id', 7)->get();
        $item_name_list = [];

        foreach ($item_names as $item) {
            $item_name_list[] = [
                'id' => $item->id,
                'name' => ItemName::findOrFail($item->item_name_id)->item_name,
            ];
        }

        $this->item_name_list = $item_name_list;

        $this->date_needed = date('Y-m-d', strtotime('+5 days'));
        $this->date_requested = date('Y-m-d');

        $ws = WS::where('farm_location_id', $this->farm_location_id)
            ->where('department_division_id', $this->department_division_id)
            ->where('active_status', 1)
            ->first();

        if ($ws) {
            $from = $ws->from;
            $to = $ws->to;
            $this->randNum = $this->findNextAvailableNumber($from, $to);

            if ($this->randNum === null) {
                $this->no_series_available = true;
            }

            $strRandNum = $this->randNum . '';
            $this->series_number .= str_pad($strRandNum, strlen($strRandNum) + 2, '0', STR_PAD_LEFT);
        }
    }

    // Function to find the next available number
    function findNextAvailableNumber($from, $to) {
        $existingNumbers = DB::table('used_series')->pluck('used_series')->toArray();

        for ($i = $from; $i <= $to; $i++) {
            if (!in_array($i, $existingNumbers)) {
                return $i;
            }
        }

        return null; // No available number found in the range
    }

    public function setSeries()
    {
        $farm_code   = "";
        $dept_code   = "";
        $id_list = WS::where('farm_location_id', $this->farm_location_id)
              ->pluck('department_division_id')->toArray();

        $this->department_division_list = DepartmentDivision::whereIn('id', $id_list)->get();

        if(isset($this->department_division_id) && isset($this->farm_location_id))
        {
            $farm_code   = FarmLocation::findorfail($this->farm_location_id)->abbreviation;
            $dept_code   = DepartmentDivision::findorfail($this->department_division_id)->abbreviation;
            $with_series = WS::where('farm_location_id', $this->farm_location_id)
                ->where('department_division_id', $this->department_division_id)->first();

            $from = $with_series->from;
            $to = $with_series->to;

            // Find the first available number in the range [from, to]
            $this->randNum = $this->findNextAvailableNumber($from, $to);


            if ($this->randNum === null) {
                // Handle the case where no available number was found in the range
                // You can add appropriate error handling here.
            }

            $strRandNum = $this->randNum . '';

            $this->series_number = $farm_code . "-" . $dept_code . "-" . str_pad($strRandNum, strlen($strRandNum) + 2, '0', STR_PAD_LEFT);
        }
        elseif (isset($this->farm_location_id))
        {
            $farm_code   = FarmLocation::findorfail($this->farm_location_id)->abbreviation;
            $this->series_number = "$farm_code-DEPT-0000000";
        }
        elseif (isset($this->department_division_id))
        {
            $dept_code   = DepartmentDivision::findorfail($this->department_division_id)->abbreviation;
            $this->series_number = "FLOC-$dept_code-0000000";
        }
    }

    public function addAnotherRow()
    {
        $this->items_requested[] = [
            'item_id' => null,
            'item_image' => '
                <img src="https://upload.wikimedia.org/wikipedia/commons/1/14/Product_sample_icon_picture.png" alt="Click to zoom" class="img-thumbnail" data-toggle="modal" data-target="#imageModal" style="width: 80px; height: 80px; max-width: 80px; max-height: 80px;">
                <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/1/14/Product_sample_icon_picture.png" class="img-fluid" alt="Image" style="width: 80%; height: 80%; max-width: 80%; max-height: 80%;">
                            </div>
                        </div>
                    </div>
                </div>
            ',
            'item_name' => null,
            'item_category' => null,
            'item_subcategory' => null,
            'item_product' => null,
            'uom_id' => null,
            'rem_quan' => null,
            'reorder' => null,
            'item_quantity' => null
        ];
    }

    public function removeRow($index)
    {
        unset($this->items_requested[$index]);
        $this->items_requested = array_values($this->items_requested);
    }

    public function automate_input($index)
    {
        $items_requested = $this->items_requested;
        $item_name = ItemName::findorfail($items_requested[$index]['item_name']);
        $item = IT::where('item_name_id', $item_name->id)->first();

        $this->items_requested[$index] = [
            'item_id' => $item_name->id,
            'item_image' => '
                <img src="' . asset('photos/' . $item->item_image) . '" alt="Click to zoom" class="img-thumbnail" data-toggle="modal" data-target="#imageModal'.md5($item->id).'" style="width: 80px; height: 80px; max-width: 80px; max-height: 80px;">
                <div class="modal fade" id="imageModal'.md5($item->id).'" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
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
            'item_name' => $items_requested[$index]['item_name'],
            'item_category' => Category::findorfail($item_name->category_id)->category_name,
            'item_subcategory' => SubCategory::findorfail($item_name->subcategory_id)->subcategory_name,
            'item_product' => Product::findorfail($item_name->product_id)->product_name,
            'uom_id' => $item->uom_id,
            'rem_quan' => $item->quantity,
            'reorder' => $item->reorder_threshold,
            'item_quantity' => $items_requested[$index]['item_quantity']
        ];


    }

    // Function that will Search for farms with the available requested items
    public function searchFarms()
    {
        $this->farm_location_list = FarmLocation::all();
    }

    public function createNewRecord()
    {

        // test if validator fails
        try{
            $this->validate();
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['For Approval', 'FOR APPROVAL', 'for approval'])
                ->first();

            if ($approval) {

                try {
                    $new_series = new US();
                    $new_series->used_series = $this->randNum;
                    $new_series->farm_location = FarmLocation::findorfail($this->farm_location_id)->farm_location;
                    $new_series->department_division = DepartmentDivision::findorfail($this->department_division_id)->department_division;

                    if ($new_series->save()) {

                        $log_entry = [
                            'New',
                            'Used Series',
                            '',
                            $new_series,
                        ];
                        AC::logEntry($log_entry);


                        $this->sampel = 1;
                        $req_items = new RI();
                        $req_items->series_number                  = $this->series_number;
                        $req_items->requested_by_id                = Auth::id();
                        $req_items->farm_location_id               = $this->farm_location_id;
                        $req_items->department_division_id         = $this->department_division_id;

                        $this->approval_id = $approval->id;

                        $req_items->approval_id                    = $this->approval_id;
                        $req_items->remarks                        = $this->remarks;
                        $req_items->date_requested                 = $this->date_requested;
                        $req_items->date_needed                    = $this->date_needed;

                        $this->uploadPdf();

                        $req_items->jl_pdf                          = $this->pdfName;

                        $req_items->active_status = 1;
                        $req_items->deleted_status = 0;


                        $req_items->save();

                        $log_entry = [
                            'Request',
                            'Request Item',
                            '',
                            $req_items,
                        ];
                        AC::logEntry($log_entry);

                        $ctr = 0;

                        while($ctr < count($this->items_requested))
                        {
                            $items_req = new IL();

                            $items_req->request_item_id = $req_items->id;
                            $items_req->item_id = $this->items_requested[$ctr]['item_id'];

                            $items_req->uom_id = empty($this->items_requested[$ctr]['uom_id']) ? NULL : $this->items_requested[$ctr]['uom_id'];
                            $items_req->item_quantity = empty($this->items_requested[$ctr]['item_quantity']) ? NULL : $this->items_requested[$ctr]['item_quantity'];
                            $items_req->active_status = 1;
                            $items_req->deleted_status = 0;

                            $items_req->save();

                            $log_entry = [
                                'Create',
                                'Item List Requested',
                                '',
                                $items_req,
                            ];
                            AC::logEntry($log_entry);

                            $ctr++;
                        }

                        session()->flash('success', 'Request Successfully Submitted!');
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    session()->flash('failed', 'Series Not Yet Assigned. Please Contact CENWH Keeper');
                }

            } else {
                session()->flash('failed', 'For Approval not found in the Approvals Module.');
            }

            return redirect('/request/item');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Request Items!');
            return redirect('/request/item');
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

    public function render()
    {
        return view('livewire.request-item.request-item');
    }

}
