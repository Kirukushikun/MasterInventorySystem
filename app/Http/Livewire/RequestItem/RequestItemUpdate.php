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
use App\Models\Product;
use App\Models\ItemList as IL;
use App\Models\Item as IT;
use App\Models\UnitOfMeasurement as UOM;
use App\Models\Approvals;
use App\Models\ItemName;
use App\Models\User;
use App\Models\UsedSeries as US;
use App\Models\RequestItem as RI;
use Illuminate\Support\Facades\DB;
use Crypt;

class RequestItemUpdate extends Component
{
    public $series_number;
    public $requested_by_id;
    public $farm_location_id;
    public $department_division_id;
    public $approval_id;
    public $remarks;
    public $date_requested;
    public $date_needed;
    public $active_status;
    public $deleted_status;

    public $request_items_id;

    public $name;
    public $randNum;
    public $rand_action = "NO";
    public $items_requested = [];

    public $listeners = [];
    public $farm_location_list;
    public $department_division_list;
    public $uom_list;
    public $sampel;
    public $item_name_list;

    public function rules()
    {
        $rules = [
            'series_number' => 'required|string',
            'farm_location_id' => 'required|integer',
            'department_division_id' => 'required|integer',
            'date_requested' => 'required|string',
            'date_needed' => 'required|string',
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

                    // $itemOnHandQty = $item['rem_quan'];

                    // // Check if Qty inputted is more than Qty-on-hand
                    // if ($value > $itemOnHandQty) {
                    //     $fail("Qty inputted is more than Qty-on-hand.");
                    // }

                    // // Check if Qty-On-Hand is equal or less than Inputted Qty
                    // if ($itemOnHandQty == $value) {
                    //     $fail("Qty-On-Hand is equal Inputted Qty.");
                    // }

                    // // Check if Inputted Qty is less than or equal to Re-order Qty
                    // $reorderQty = $item['reorder'];

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

    public function messages()
    {
        return [
            'items_requested.*.item_name.required' => 'Item Name is required',
            'items_requested.*.item_quantity.required' => 'Quantity is required',
        ];
    }

    public function mount($id)
    {
        $this->listeners = ['editRecord'];
        $this->request_items_id = $id;
        $req_items = RI::findorfail($id);

        $this->farm_location_list = FarmLocation::where('active_status', 1)->get();
        $this->department_division_list = DepartmentDivision::where('active_status', 1)->get();
        $this->uom_list = UOM::where('active_status', 1)->get();

        $item_name_list = IT::where('active_status', 1)->where('approval_id', 7)->get();

        foreach ($item_name_list as $key => $item_name) {
            $this->item_name_list[] = [
                'id' => $item_name->id,
                'name' => ItemName::findorfail($item_name->item_name_id)->item_name,
            ];
        }

        $string = $req_items->series_number;
        $parts = explode('-', $string);

        if (count($parts) >= 3)
        {
            $desiredValue = $parts[2];
            $this->randNum = (int) $desiredValue;
        }

        $this->series_number = $req_items->series_number;
        $this->name = GC::getUserFullName($req_items->requested_by_id);
        $this->requested_by_id = $req_items->requested_by_id;
        $this->farm_location_id = $req_items->farm_location_id;
        $this->department_division_id = $req_items->department_division_id;
        $this->date_needed = $req_items->date_needed;
        $this->date_requested = $req_items->date_requested;
        $this->approval_id = $req_items->approval_id;
        $this->remarks = $req_items->remarks;

        $this->req_list = IL::where('active_status', 1)->where('request_item_id', $id)->get()->toArray();

        foreach($this->req_list as $key => $req_list)
        {

            $item_name = ItemName::findorfail($this->req_list[$key]['item_id']);
            $item = IT::where('item_name_id', $item_name->id)->first();

            $this->items_requested[] = [
                'title' => null,
                'item_id' =>  $this->req_list[$key]['item_id'],
                'item_name' => $this->req_list[$key]['item_id'],
                'item_category'    => Category::findorfail(ItemName::findorfail($this->req_list[$key]['item_id'])->category_id)->category_name,
                'item_subcategory' => SubCategory::findorfail(ItemName::findorfail($this->req_list[$key]['item_id'])->subcategory_id)->subcategory_name,
                'item_product'     => Product::findorfail(ItemName::findorfail($this->req_list[$key]['item_id'])->product_id)->product_name,
                'uom_id' => $this->req_list[$key]['uom_id'],
                'rem_quan' => $item->quantity,
                'reorder'  => $item->reorder_threshold,
                'item_quantity' => $this->req_list[$key]['item_quantity']
            ];
        }
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
            'title' => null,
            'item_id' => null,
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

    public function editRecord()
    {
        // test if validator fails
        try{
            $this->validate();
            $req_items = RI::findorfail($this->request_items_id);

            $req_items->series_number                  = $this->series_number;
            $req_items->requested_by_id                = Auth::id();
            $req_items->farm_location_id               = $this->farm_location_id;
            $req_items->department_division_id         = $this->department_division_id;
            $approval = Approvals::where('active_status', 1)
                ->whereIn('title', ['For Approval', 'FOR APPROVAL', 'for approval'])
                ->first();

            if ($approval) {
                $this->approval_id = $approval->id;

                $req_items->approval_id                    = $this->approval_id;
                $req_items->remarks                        = $this->remarks;
                $req_items->date_requested                 = $this->date_requested;
                $req_items->date_needed                    = $this->date_needed;

                $req_items->active_status = 1;
                $req_items->deleted_status = 0;

                $req_items->save();

                $log_entry = [
                    'Update',
                    'Request Item',
                    '',
                    $req_items,
                ];
                AC::logEntry($log_entry);

                if($this->rand_action == "YES")
                {
                    $new_series = new US();
                }else{
                    $new_series_id = US::where('used_series', $this->randNum)->get()->first()->id;
                    $new_series    = US::findorfail($new_series_id);
                }

                $new_series->used_series           = $this->randNum;
                $new_series->farm_location         = FarmLocation::findorfail($this->farm_location_id)->farm_location;
                $new_series->department_division   = DepartmentDivision::findorfail($this->department_division_id)->department_division;

                $new_series->save();

                $log_entry = [
                    'Update',
                    'Used Series',
                    '',
                    $new_series,
                ];
                AC::logEntry($log_entry);

                $ctr = 0;
                $count_of_deleted = IL::where('request_item_id', $this->request_items_id)->delete();

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
                        'Update',
                        'Item List Requested',
                        '',
                        $items_req,
                    ];
                    AC::logEntry($log_entry);

                    $ctr++;
                }
                $this->reset(['series_number', 'farm_location_id', 'department_division_id', 'approval_id', 'remarks', 'date_requested', 'date_needed']);
                $this->items_requested = [];

                session()->flash('success', 'Request has been Updated!');
            } else {
                session()->flash('failed', 'For Approval not found in the Approvals Module.');
            }

            return redirect('/request/item/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Requested Items!');
            return redirect('/request/item/list');
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
        return view('livewire.request-item.request-item-update');
    }
}
