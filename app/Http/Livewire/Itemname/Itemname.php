<?php

namespace App\Http\Livewire\Itemname;

use Livewire\Component;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\ItemName as ITNAME;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class Itemname extends Component
{
    public $category_id;
    public $subcategory_id;
    public $product_id;
    public $category_list;
    public $subcategory_list;
    public $product_list;
    public $item_name;
    public $item_description;
    public $listeners = [];
    public $via_item = null;

    protected function rules()
    {
        $all_data = ITNAME::where('active_status', 1)->get();
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => 'required|integer|exists:sub_categories,id',
            'product_id' => 'required|integer|exists:products,id',

            'item_name' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if ($field_value->item_name == $value) {
                            $fail("This Item Name Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'item_description' => 'nullable|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'The field "Category:" is Required',
            'subcategory_id.required' => 'The field "Sub Category:" is Required',
            'product_id.required' => 'The field "Product:" is Required',
            'item_name.required' => 'The field "Item Name:" is Required',
        ];
    }

    public function mount($formData = null)
    {
        // call functionCreateNewRecord
        $this->listeners = ['createNewRecord'];

        $this->category_list = CT::where('active_status', 1)->get();
        $this->subcategory_list = null;//SCT::where('active_status', 1)->get();
        $this->product_list = null;//PRD::where('active_status', 1)->get();
    }

    public function set_subcategory()
    {
        $category_id = $this->category_id;
        $this->subcategory_list = SCT::where('category_id', $category_id)->where('active_status', 1)->get();
    }

    public function set_product()
    {
        $category_id = $this->category_id;
        $subcategory_id = $this->subcategory_id;
        $this->product_list = PRD::where('category_id', $category_id)
                                ->where('subcategory_id', $subcategory_id)->where('active_status', 1)
                                ->get();
    }

    public function createNewRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database
            $item = new ITNAME();

            $similarity_percentage = GC::compare_strings(strtoupper($this->item_name),'item_names','item_name');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('category', $this->category_id);
                session()->flash('subcategory', $this->subcategory_id);
                session()->flash('product', $this->product_id);
                session()->flash('already_exist', '<i>"' . strtoupper($this->item_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/itemname');
            }

            $item->category_id = $this->category_id;
            $item->subcategory_id = $this->subcategory_id;
            $item->product_id = $this->product_id;
            $item->item_name = $this->item_name;
            $item->item_description = $this->item_description;
            $item->active_status = true;
            $item->deleted_status = false;
            $item->save();
        
            // create log for the data
            $log_entry = [
                'Create',
                'item Name',
                '',
                $item,
            ];
            AC::logEntry($log_entry);

            if ($this->via_item == 'item') {
                session()->flash('success_two', 'New Item Name Succesfully Created!');
                return redirect('/itemname');
            }
            session()->flash('success', 'New Item Name Succesfully Created!');
            return redirect('/itemname');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Item Name!');
            return redirect('/itemname');
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
        return view('livewire.itemname.itemname');
    }
}
