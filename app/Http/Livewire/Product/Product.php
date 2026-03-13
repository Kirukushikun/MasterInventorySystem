<?php

namespace App\Http\Livewire\Product;

use Livewire\Component;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class Product extends Component
{
    public $category_id;
    public $subcategory_id;
    public $category_list;
    public $subcategory_list;
    public $product_name;
    public $product_description;
    public $listeners = [];
    public $via_item = null;

    protected function rules()
    {
        $all_data = PRD::where('active_status', 1)->get();
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => 'required|integer|exists:sub_categories,id',
            'product_name' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->product_name) == strtolower($value)) {
                            $fail("This Product Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'product_description' => 'nullable|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'The field "Category:" is Required',
            'subcategory_id.required' => 'The field "Sub Category:" is Required',
            'product_name.required' => 'The field "Product Name:" is Required',
        ];
    }

    public function mount($formData = null)
    {
        // call functionCreateNewRecord
        $this->listeners = ['createNewRecord'];

        $this->category_list = CT::where('active_status', 1)->get();
        $this->subcategory_list = null;//SCT::where('active_status', 1)->get();
    }

    public function set_subcategory()
    {
        $category_id = $this->category_id;
        $this->subcategory_list = SCT::where('category_id', $category_id)->where('active_status', 1)->get();
    }

    public function createNewRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database
            $product_name = new PRD();

            $similarity_percentage = GC::compare_strings(strtoupper($this->product_name), 'products', 'product_name');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->product_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/product');
            }

            $product_name->category_id = $this->category_id;
            $product_name->subcategory_id = $this->subcategory_id;
            $product_name->product_name = $this->product_name;
            $product_name->product_description = $this->product_description;
            $product_name->active_status = true;
            $product_name->deleted_status = false;
            $product_name->save();
            // create log for the data
            $log_entry = [
                'Create',
                'Product',
                '',
                $product_name,
            ];
            AC::logEntry($log_entry);

            if ($this->via_item == 'item') {
                session()->flash('success_two', 'New Product Succesfully Created!');
                return redirect('/product');
            }
            session()->flash('success', 'New Product Succesfully Created!');
            return redirect('/product');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Product!');
            return redirect('/product');
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
        return view('livewire.product.product');
    }
}
