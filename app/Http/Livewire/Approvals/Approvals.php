<?php

namespace App\Http\Livewire\Approvals;

use Livewire\Component;

use App\Models\Approvals as AP;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class Approvals extends Component
{
    public $title;
    public $description;
    public $listeners = [];

    protected function rules()
    {
        $all_data = AP::where('active_status', 1)->get();
        return [
            'title' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->title) == strtolower($value)) {
                            $fail("This Approvals Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'description' => 'nullable|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The field "Title:" is Required',
            'description.required' => 'The field "Description:" is Required',
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

    public function mount($formData = null)
    {
        // call functionCreateNewRecord
        $this->listeners = ['createNewRecord'];
    }

    public function createNewRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database
            $title = new AP();

            $similarity_percentage = GC::compare_strings(strtoupper($this->title),'approvals','title');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->title) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/approvals');
            }

            $title->title = strtoupper($this->title);
            $title->description = strtoupper($this->description);
            $title->active_status = true;
            $title->deleted_status = false;
            $title->save();
            // create log for the data
            $log_entry = [
                'Create',
                'Approvals',
                '',
                $title,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'New Approvals Succesfully Created!');

            // reset form fields
            $this->reset(['title', 'description']);

            return redirect('/approvals');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Approvals!');
            return redirect('/approvals');
        }
    }

    public function render()
    {
        return view('livewire.approvals.approvals');
    }
}
