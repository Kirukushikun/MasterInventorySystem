<?php

namespace App\Http\Livewire\Alert;

use Livewire\Component;

use App\Models\Category as CT;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class CategorySweetAlertForm extends Component
{
    public $category_name;
    public $category_description;

    protected function rules()
    {
        $all_data = CT::where('active_status', 1)->get();
        return [
            'category_name' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower(field_value->category_name) == strtolower($value)) {
                            $fail("This Category Name Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'category_description' => 'nullable|string|min:0|max:255',
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

    public function messages()
    {
        return [
            'category_name.required' => 'The field "Category Name:" is Required',
        ];
    }


    public function submitForm()
    {
        $this->validate();

        // create new record in the database
        $category_name = new CT();

        $similarity_percentage = GC::compare_strings(strtoupper($this->category_name),'categories','category_name');

        if ($similarity_percentage !== false && $similarity_percentage >= 95) {
            session()->flash('already_exist', '<i>"' . strtoupper($this->category_name) . '"</i> Already Exists or has Similar Data in the List.');
            return redirect('/category');
        }

        $category_name->category_name = $this->category_name;
        $category_name->category_description = $this->category_description;
        $category_name->active_status = true;
        $category_name->deleted_status = false;
        $category_name->save();

        // create log for the data
        $log_entry = [
            'Create',
            'Category',
            '',
            $category_name,
        ];
        AC::logEntry($log_entry);
    }
    
    public function render()
    {
        return view('livewire.alert.category-sweet-alert-form');
    }
}
