<?php

namespace App\Http\Livewire\TransactionType;

use Livewire\Component;

use App\Models\TransactionType as TT;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\GeneralController as GC;
use Crypt;

class TransactionTypeUpdate extends Component
{

    public $transaction_type;
    public $transaction_type_description;
    public $listeners = [];

    public $category_id;

    protected function rules()
    {
        $all_data = TT::where('active_status', 1)->where('id', '!=', $this->category_id)->get();
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
            'transaction_type.unique' => 'This Transaction Type Already Exists',
        ];
    }

    public function mount($id)
    {
        // call functionCreateNewRecord
        $this->listeners = ['editRecord'];

        $this->category_id = $id;
        $category = TT::findorfail($id);
        $this->transaction_type = $category->transaction_type;
        $this->transaction_type_description = $category->transaction_type_description;
    }

    public function editRecord()
    {

        // test if validator fails
        try{
            // validate inputs here
            $this->validate();

            // edit record in the database
            $transaction_type = TT::findorfail($this->category_id);
            $transaction_type_old = $transaction_type;

            $similarity_percentage = GC::compare_strings(strtoupper($this->transaction_type),'transaction_types','transaction_type', $this->category_id);

            if ($similarity_percentage !== false && $similarity_percentage >= 95) {
                session()->flash('already_exist', '<i>"' . strtoupper($this->transaction_type) . '"</i> Already Exists or has Similar Data in the List.');
                return redirect('/transaction/type/show' . '/' . Crypt::encryptString($this->category_id));
            }

            $transaction_type->transaction_type = $this->transaction_type;
            $transaction_type->transaction_type_description = $this->transaction_type_description;
            $transaction_type->active_status = true;
            $transaction_type->deleted_status = false;

            $transaction_type->save();
            // create log for the data
            $log_entry = [
                'Update',
                'TransactionType',
                $transaction_type_old,
                $transaction_type,
            ];
            AC::logEntry($log_entry);

            // display success message to user
            session()->flash('success', 'Transaction Type Succesfully Updated!');

            // reset form fields
            $this->reset(['transaction_type', 'transaction_type_description']);

            return redirect('/transaction/type');

        }catch (ValidationException $e) {

            // Validation failed, handle the error
            session()->flash('failed', 'Failed to Update Transaction Type!');
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
        return view('livewire.transaction-type.transaction-type-update');
    }
}
