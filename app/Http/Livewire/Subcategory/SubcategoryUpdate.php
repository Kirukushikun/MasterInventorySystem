<?php

namespace App\Http\Livewire\Subcategory;

use Livewire\Component;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class SubcategoryUpdate extends Component
{
    public $category_id;
    public $category_list;
    public $subcategory_name;
    public $subcategory_description;
    public $listeners = [];
    public $via_item = null;

    public $subcategory_id;

    protected function rules()
    {
        $all_data = SCT::where('active_status', 1)->where('id', '!=', $this->subcategory_id)->get();
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_name' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->subcategory_name) == strtolower($value)) {
                            $fail("This Sub Category Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'subcategory_description' => 'nullable|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'The field "Category:" is Required',
            'subcategory_name.required' => 'The field "Sub Category Name:" is Required',
            'subcategory_name.unique' => 'This Sub-Category Name Already Exist',
        ];
    }

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners = ['editRecord'];
        
        $this->category_list = CT::where('active_status', 1)->get();

        $this->subcategory_id = $id;
        $subcategory = SCT::findorfail($id);
        $this->category_id = $subcategory->category_id;
        $this->subcategory_name = $subcategory->subcategory_name;
        $this->subcategory_description = $subcategory->subcategory_description;
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // edit record in the database
            $subcategory_name = SCT::findorfail($this->subcategory_id);

            $similarity_percentage = GC::compare_strings(strtoupper($this->subcategory_name),'sub_categories','subcategory_name', $this->subcategory_id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->subcategory_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/subcategory/show' . '/' . Crypt::encryptString($subcategory_name->id));
            }

            $subcategory_name->category_id = $this->category_id;
            $subcategory_name->subcategory_name = $this->subcategory_name;
            $subcategory_name->subcategory_description = $this->subcategory_description;
            $subcategory_name->active_status = true;
            $subcategory_name->deleted_status = false;

            $subcategory_name->save();
            // create log for the data
            $log_entry = [
                'Update',
                'Sub Category',
                '',
                $subcategory_name,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Sub Category Succesfully Updated!');

            return redirect('/subcategory/list');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Sub Category!');
            return redirect('/subcategory/list');
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
        return view('livewire.subcategory.subcategory-update');
    }
}
