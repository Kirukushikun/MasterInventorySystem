<?php

namespace App\Http\Livewire\Supplier;

use Livewire\Component;

use App\Models\Supplier as SPL;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class Supplier extends Component
{
    public $supplier_name;
    public $contact_person;
    public $contact_email;
    public $contact_phone;
    public $contact_tel_no;
    public $listeners = [];
    public $via_item = null;

    protected function rules()
    {
        $all_data = SPL::where('active_status', 1)->get();
        return [
            'supplier_name' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->supplier_name) == strtolower($value)) {
                            $fail("This Supplier Name Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'contact_person' => 'required|string|min:1|max:100',
            'contact_tel_no' => 'nullable|string|min:0|max:100',
            'contact_email' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->contact_email) == strtolower($value)) {
                            $fail("This Contact Email Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'contact_phone' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->contact_phone) == strtolower($value)) {
                            $fail("This Contact Phone Already Exist");
                            break;
                        }
                    }
                }, 
            ],
        ];
    }

    public function messages()
    {
        return [
            'supplier_name.required' => 'The Supplier Name field is required.',
            'contact_person.required' => 'The Contact Person field is required.',
            'contact_email.required' => 'The Contact Email field is required.',
            'contact_email.email' => 'The Contact Email must be a valid email address.',
            'contact_phone.required' => 'The Contact Phone field is required.',
            'contact_phone.regex' => 'The Contact Phone must be a valid format, starting with +639 and followed by 9 digits (e.g. +639*********).',
        ];
    }


    public function mount()
    {
        // call functionCreateNewRecord
        $this->listeners=  ['createNewRecord'];
    }

    public function createNewRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // create new record in the database
            $supplier = new SPL();

            $similarity_percentage = GC::compare_strings(strtoupper($this->supplier_name),'suppliers','supplier_name');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->supplier_name) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/supplier');
            }

            $supplier->supplier_name = $this->supplier_name;
            $supplier->contact_person = $this->contact_person;
            $supplier->contact_email = $this->contact_email;
            $supplier->contact_phone = $this->contact_phone;
            $supplier->contact_tel_no = $this->contact_tel_no;
            $supplier->active_status = true;
            $supplier->deleted_status = false;
            $supplier->save();
            
            // create log for the data
            $log_entry = [
                'Create',
                'Supplier',
                '',
                $supplier,
            ];
            AC::logEntry($log_entry);

            if ($this->via_item == 'item') {
                session()->flash('success_two', 'New Supplier Succesfully Created!');
                return redirect('/supplier');

            }
            session()->flash('success', 'New Supplier Succesfully Created!');
            return redirect('/supplier');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Supplier!');
            return redirect('/supplier');
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
        return view('livewire.supplier.supplier');
    }
}
