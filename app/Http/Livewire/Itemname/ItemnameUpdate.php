<?php

namespace App\Http\Livewire\Itemname;

use Livewire\Component;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\ItemName;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class ItemnameUpdate extends Component
{
    public $category_id;
    public $category_list;
    public $subcategory_id;
    public $subcategory_list;
    public $product_id;
    public $product_list;

    public $item_name;
    public $item_description;
    public $listeners = [];

    public $item_id;

    protected function rules()
    {
        $all_data = ItemName::where('active_status', 1)->where('id', '!=', $this->item_id)->get();
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
                        if (strtolower($field_value->item_name) == strtolower($value)) {
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
            'item_name.unique' => 'This Item Name Alreaddy Exist',
        ];
    }

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners = ['editRecord'];
        
        $this->category_list = CT::where('active_status', 1)->get();
        $this->subcategory_list = SCT::where('active_status', 1)->get();
        $this->product_list = PRD::where('active_status', 1)->get();


        $this->item_id = $id;
        $item = ItemName::findorfail($id);
        $this->category_id = $item->category_id;
        $this->subcategory_id = $item->subcategory_id;
        $this->product_id = $item->product_id;
        $this->item_name = $item->item_name;
        $this->item_description = $item->item_description;


        $this->subcategory_list = SCT::where('category_id', $this->category_id)->where('active_status', 1)->get();
        $this->product_list = PRD::where('category_id', $this->category_id)
                                ->where('subcategory_id', $this->subcategory_id)->where('active_status', 1)
                                ->get();
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

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // edit record in the database
            $item = ItemName::findorfail($this->item_id);
            $item_old = $item;

            $similarity_percentage = GC::compare_strings(strtoupper($this->item_name),'item_names','item_name', $item->id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->item_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect()->route('itemname.div.show', ['id' => Crypt::encryptString($item->id)]);
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
                'Update',
                'Item Name',
                $item_old,
                $item,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Item Name Succesfully Updated!');
            return redirect('/itemname/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Item Name!');
            return redirect('/itemname/list');
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
        return view('livewire.itemname.itemname-update');
    }
}
