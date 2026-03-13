<?php

namespace App\Http\Livewire\Subcategory;

use Livewire\Component;

use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Illuminate\Validation\ValidationException;

class Subcategory extends Component
{
    public $category_id;
    public $category_list;
    public $subcategory_name;
    public $subcategory_description;
    public $listeners = [];
    public $via_item = null;

    protected function rules()
    {
        $all_data = SCT::where('active_status', 1)->get();
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
        ];
    }

    public function mount($formData = null)
    {
        // call functionCreateNewRecord
        $this->listeners = ['createNewRecord'];

        $this->category_list = CT::where('active_status', 1)->get();
    }

    public function createNewRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database
            $subcategory_name = new SCT();

            $similarity_percentage = GC::compare_strings(strtoupper($this->subcategory_name),'sub_categories','subcategory_name');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->subcategory_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/subcategory');
            }

            $subcategory_name->category_id = $this->category_id;
            $subcategory_name->subcategory_name = $this->subcategory_name;
            $subcategory_name->subcategory_description = $this->subcategory_description;
            $subcategory_name->active_status = true;
            $subcategory_name->deleted_status = false;
            $subcategory_name->save();

            // create log for the data
            $log_entry = [
                'Create',
                'Sub Category',
                '',
                $subcategory_name,
            ];
            AC::logEntry($log_entry);

            if ($this->via_item == 'item') {
                session()->flash('success_two', 'New Sub Category Succesfully Created!');
                return redirect('/subcategory');
            }
            session()->flash('success', 'New Sub Category Succesfully Created!');
            return redirect('/subcategory');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Sub Category!');
            return redirect('/subcategory');
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
        return view('livewire.subcategory.subcategory');
    }
}
