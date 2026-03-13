<?php

namespace App\Http\Livewire\Product;

use Livewire\Component;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class ProductUpdate extends Component
{
    public $category_id;
    public $category_list;
    public $subcategory_id;
    public $subcategory_list;

    public $product_name;
    public $product_description;
    public $listeners = [];

    public $product_id;

    protected function rules()
    {
        $all_data = PRD::where('active_status', 1)->where('id', '!=', $this->product_id)->get();
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
            'product_name.required' => 'The field "Sub Category Name:" is Required',
            'product_name.unique' => 'This Product Name Already Exist',
        ];
    }

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners = ['editRecord'];
        
        $this->category_list = CT::where('active_status', 1)->get();
        $this->subcategory_list = SCT::where('active_status', 1)->get();

        $this->product_id = $id;
        $product = PRD::findorfail($id);
        $this->category_id= $product->category_id;
        $this->subcategory_id = $product->subcategory_id;
        $this->product_name = $product->product_name;
        $this->product_description = $product->product_description;
        
        $this->subcategory_list = SCT::where('category_id', $this->category_id)->where('active_status', 1)->get();
    }

    public function set_subcategory()
    {
        $category_id = $this->category_id;
        $this->subcategory_list = SCT::where('category_id', $category_id)->where('active_status', 1)->get();
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // edit record in the database
            $product_name = PRD::findorfail($this->product_id);
            $product_name_old = $product_name;

            $similarity_percentage = GC::compare_strings(strtoupper($this->product_name), 'products', 'product_name', $this->product_id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->product_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect()->route('product.div.show', ['id' => Crypt::encryptString($product_name->id)]);
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
                'Update',
                'Product',
                $product_name_old,
                $product_name,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Product Succesfully Updated!');

            return redirect('/product/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Product!');
            return redirect('/product/list');
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
        return view('livewire.product.product-update');
    }
}
