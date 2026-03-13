<?php

namespace App\Http\Livewire\Approvals;

use Livewire\Component;

use App\Models\Approvals as AP;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;
class ApprovalsUpdate extends Component
{
    public $title;
    public $description;
    public $listeners = [];
    public $approval_id;

    protected function rules()
    {
        $all_data = AP::where('active_status', 1)->where('id', '!=', $this->approval_id)->get();
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
            'description' => 'required|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The field "Title:" is Required',
            'title.unique' => 'This Title Already Exist',
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

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners = ['editRecord'];

        $this->approval_id = $id;
        $approvals = AP::findorfail($id);
        $this->title = $approvals->title;
        $this->description = $approvals->description;
    }

    public function editRecord()
    {
        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // edit record in the database
            $title = AP::findorfail($this->approval_id);
            $title_old = $title;

            $similarity_percentage = GC::compare_strings(strtoupper($this->title),'approvals','title', $title->id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->title) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect()->route('approvals.div.show', ['id' => Crypt::encryptString($title->id)]);
            }

            $title->title = strtoupper($this->title);
            $title->description = strtoupper($this->description);
            $title->active_status = true;
            $title->deleted_status = false;
            $title->save();

            // create log for the data
            $log_entry = [
                'Update',
                'Approvals',
                $title_old,
                $title,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Approvals Succesfully Updated!');

            // reset form fields
            $this->reset(['title', 'description']);

            return redirect('/approvals/list')->with('success', 'Approvals Succesfully Updated!');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Approvals!');
            return redirect('/approvals/list')->with('failed', 'Failed to Update Approvals!');
        }
    }

    public function render()
    {
        return view('livewire.approvals.approvals-update');
    }
}
