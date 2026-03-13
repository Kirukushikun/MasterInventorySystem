<?php

namespace App\Http\Livewire\Category;

use Livewire\Component;

use App\Models\Category as CT;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class CategoryUpdate extends Component
{

    public $category_name;
    public $category_description;
    public $listeners = [];

    public $category_id;

    protected function rules()
    {
        $all_data = CT::where('active_status', 1)->where('id', '!=', $this->category_id)->get();
        return [
            'category_name' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->category_name) == strtolower($value)) {
                            $fail("This Category Name Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'category_description' => 'nullable|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'category_name.required' => 'The field "Category Name:" is Required',
            'category_name.unique' => 'This Category Name Already Exist',
        ];
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

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners = ['editRecord'];

        $this->category_id = $id;
        $category = CT::findorfail($id);
        $this->category_name = $category->category_name;
        $this->category_description = $category->category_description;
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // edit record in the database
            $category_name = CT::findorfail($this->category_id);
            $category_name_old = CT::findorfail($this->category_id);

            $similarity_percentage = GC::compare_strings(strtoupper($this->category_name), 'categories', 'category_name', $category_name->id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->category_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect()->route('category.div.show', ['id' => Crypt::encryptString($category_name->id)]);
            }

            $category_name->category_name = $this->category_name;
            $category_name->category_description = $this->category_description;
            $category_name->active_status = true;
            $category_name->deleted_status = false;

            $category_name->save();
            // create log for the data
            $log_entry = [
                'Update',
                'Category',
                $category_name_old,
                $category_name,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Category Succesfully Updated!');

            return redirect('/category/list')->with('success', 'New Category Succesfully Updated!');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Category!');
            return redirect('/category/list')->with('failed', 'Failed to Update Category!');
        }
    }

    public function render()
    {
        return view('livewire.category.category-update');
    }
}
