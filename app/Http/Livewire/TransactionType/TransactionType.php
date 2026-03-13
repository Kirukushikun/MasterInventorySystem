<?php

namespace App\Http\Livewire\TransactionType;

use Livewire\Component;

use App\Models\TransactionType as TT;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;

class TransactionType extends Component
{

    public $transaction_type;
    public $transaction_type_description;
    public $listeners = [];

    protected function rules()
    {
        $all_data = TT::where('active_status', 1)->get();
        return [
            'transaction_type' => [
                'required',
                'string',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($all_data) {
                    foreach ($all_data as $field_value) {
                        if (strtolower($field_value->transaction_type) == strtolower($value)) {
                            $fail("This Transaction Type Already Exist");
                            break;
                        }
                    }
                }, 
            ],
            'transaction_type_description' => 'nullable|string|min:0|max:255',
        ];
    }

    public function messages()
    {
        return [
            'transaction_type.required' => 'The field Transaction Type is Required',
        ];
    }

    public function mount($formData = null)
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
            $transaction_type = new TT();

            $similarity_percentage = GC::compare_strings(strtoupper($this->transaction_type),'transaction_types','transaction_type');

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->transaction_type) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/transaction/type');
            }

            $transaction_type->transaction_type = $this->transaction_type;
            $transaction_type->transaction_type_description = $this->transaction_type_description;
            $transaction_type->active_status = true;
            $transaction_type->deleted_status = false;
            $transaction_type->save();
            // create log for the data
            $log_entry = [
                'Create',
                'TransactionType',
                '',
                $transaction_type,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'New Transaction Type Succesfully Created!');
            return redirect('/transaction/type');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Create Transaction Type!');
            return redirect('/transaction/type');
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
        return view('livewire.transaction-type.transaction-type');
    }
}
