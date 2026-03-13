<?php

namespace App\Http\Livewire\Access;

use Livewire\Component;
use App\Models\Access as ACC;

use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Models\User;

class UserAccess extends Component
{
    public $access = array(), $db_access, $temp_acc = array(), $userID = '', $userFullName = '', $userAction = '';
    public $modules_list;

    /**
     * Initializes the temporary access variable with the current access value.
     *
     * @throws Some_Exception_Class description of exception
     */
    public function chk_box()
    {
        $this->temp_acc = $this->access;
    }

    /**
     * Mount the function and initialize the necessary variables.
     *
     * @param mixed $id The ID parameter for the function (default: null).
     * @param mixed $name The name parameter for the function (default: null).
     * @param mixed $action The action parameter for the function (default: null).
     * @throws Some_Exception_Class Description of the exception that may be thrown.
     * @return void
     */
    public function mount($id, $name, $action){
        $this->userID = $id;
        $this->userFullName = $name;
        $this->userAction = $action;

        if ($action == 'set')
        {
            $this->db_access = new ACC();
        }
        elseif ($action == 'edit')
        {
            $this->db_access = ACC::findorfail($this->userID);
            $this->db_access_old = $this->db_access;
            $this->access = array_values(array_filter(explode(',', $this->db_access->access)));
            // $this->temp_acc = $this->access;
        }

        $this->modules_list = [
            'Dashboard' => [ // OKAY
                'MODULE' => 'dash_mod',
                'ADD' => null,
                'EDIT' => null,
                'DELETE' => null,
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Category' => [ // OKAY
                'MODULE' => 'cat_mod',
                'ADD' => 'cat_add',
                'EDIT' => 'cat_edit',
                'DELETE' => 'cat_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'SubCategory' => [ // OKAY
                'MODULE' => 'subcat_mod',
                'ADD' => 'subcat_add',
                'EDIT' => 'subcat_edit',
                'DELETE' => 'subcat_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Product' => [ // OKAY
                'MODULE' => 'product_mod',
                'ADD' => 'product_add',
                'EDIT' => 'product_edit',
                'DELETE' => 'product_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Inventory' => [// OKAY
                'MODULE' => 'inventory_mod',
                'ADD' => 'inventory_add',
                'EDIT' => 'inventory_edit',
                'DELETE' => 'inventory_del',
                'CHECKOUT' => 'inventory_checkout',
                'DETAILS' => 'inventory_details',
                'MULTIPLE CHECKOUT' => 'inventory_multiple',
                'DIMINISH' => 'inventory_diminish'
            ],
            'ReOrder' => [// OKAY
                'MODULE' => 'reorder_mod',
                'EDIT' => 'reorder_edit',
                'DELETE' => null,
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => null,
                'DIMINISH' => NULL
            ],
            'FarmInventory' => [// OKAY
                'MODULE' => 'farminventory_mod',
                'ADD' => 'farminventory_add',
                'EDIT' => 'farminventory_edit',
                'DELETE' => 'farminventory_del',
                'CHECKOUT' => null,//'farminventory_checkout',
                'DETAILS' => 'farminventory_details',//'farminventory_details',
                'MULTIPLE CHECKOUT' => NULL,//'inventory_multiple'
                'DIMINISH' => NULL
            ],
            'ItemName' => [ // OKAY
                'MODULE' => 'itemname_mod',
                'ADD' => 'itemname_add',
                'EDIT' => 'itemname_edit',
                'DELETE' => 'itemname_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Transaction Type' => [ // OKAY
                'MODULE' => 'transtype_mod',
                'ADD' => 'transtype_add',
                'EDIT' => 'transtype_edit',
                'DELETE' => 'transtype_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Alert Type' => [// OKAY
                'MODULE' => 'alerttype_mod',
                'ADD' => 'alerttype_add',
                'EDIT' => 'alerttype_edit',
                'DELETE' => 'alerttype_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Location' => [ // OKAY
                'MODULE' => 'location_mod',
                'ADD' => 'location_add',
                'EDIT' => 'location_edit',
                'DELETE' => 'location_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Supplier' => [ // OKAY
                'MODULE' => 'supplier_mod',
                'ADD' => 'supplier_add',
                'EDIT' => 'supplier_edit',
                'DELETE' => 'supplier_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Withdrawal/Request' => [ // OKAY
                'MODULE' => 'withreq_mod',
                'ADD' => 'withreq_add',
                'EDIT' => 'withreq_edit',
                'DELETE' => 'withreq_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Withdrawal Series' => [ // OKAY
                'MODULE' => 'withser_mod',
                'ADD' => 'withser_add',
                'EDIT' => 'withser_edit',
                'DELETE' => 'withser_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Gate Pass Series' => [ // OKAY
                'MODULE' => 'gateser_mod',
                'ADD' => 'gateser_add',
                'EDIT' => 'gateser_edit',
                'DELETE' => 'gateser_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Farm Location' => [ // OKAY
                'MODULE' => 'farmloc_mod',
                'ADD' => 'farmloc_add',
                'EDIT' => 'farmloc_edit',
                'DELETE' => 'farmloc_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Department Division' => [ // OKAY
                'MODULE' => 'deptdiv_mod',
                'ADD' => 'deptdiv_add',
                'EDIT' => 'deptdiv_edit',
                'DELETE' => 'deptdiv_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Unit of Measurement' => [ // OKAY
                'MODULE' => 'uom_mod',
                'ADD' => 'uom_add',
                'EDIT' => 'uom_edit',
                'DELETE' => 'uom_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Approvals' => [ // OKAY
                'MODULE' => 'approvals_mod',
                'ADD' => 'approvals_add',
                'EDIT' => 'approvals_edit',
                'DELETE' => 'approvals_del',
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'For Approvals' => [ // OKAY
                'MODULE' => 'forapprovals_mod',
                'APRROVE' => 'forapprovals_approve',
                'DENY' => 'forapprovals_deny',
                'DIMINISH' => NULL,
                'CHECKOUT ITEMS' => 'forapprovals_checkout',
                'FOR RELEASE' => 'forapprovals_forrelease',
                'IN TRANSIT' => 'forapprovals_intransit',
                'RECEIVED' => 'forapprovals_received'
            ],
            'For Approvals (Farm Level Inventory Update)' => [ // OKAY
                'MODULE' => 'forapprovals_update_mod',
                'APRROVE' => 'forapprovals_update_approve',
                'DENY' => 'forapprovals_update_deny',
                'DIMINISH' => NULL,
                'CHECKOUT ITEMS' => null,
                'FOR RELEASE' => null,
                'IN TRANSIT' => null,
                'RECEIVED' => null
            ],
            'For Approvals (Inventory Creation/Additions)' => [ // OKAY
                'MODULE' => 'forapprovals_inv_create_mod',
                'APRROVE' => 'forapprovals_inv_create_approve',
                'DENY' => 'forapprovals_inv_create_deny',
                'DIMINISH' => NULL,
                'CHECKOUT ITEMS' => null,
                'FOR RELEASE' => null,
                'IN TRANSIT' => null,
                'RECEIVED' => null
            ],
            'Users' => [ // OKAY
                'MODULE' => 'users_mod',
                'GRANT' => 'users_grant',
                'EDIT' => null,
                'DELETE' => null,
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Access' => [ // OKAY
                'MODULE' => 'access_mod',
                'SET' => 'access_set',
                'EDIT' => null,
                'DELETE' => null,
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Audit' => [ // OKAY
                'MODULE' => 'audit_mod',
                'ADD' => null,
                'EDIT' => null,
                'DELETE' => null,
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],
            'Farm Check Stock Module' => [ // OKAY
                'MODULE' => 'farmcheckstock_mod',
                'ADD' => null,
                'EDIT' => null,
                'DELETE' => null,
                'CHECKOUT' => null,
                'DETAILS' => null,
                'MULTIPLE CHECKOUT' => NULL,
                'DIMINISH' => NULL
            ],

        ];
    }

    /**
     * Sets the access for the user.
     *
     * @throws Some_Exception_Class If there is an error saving the access.
     * @return void
     */
    public function set_access()
    {
        $user_id = (int) $this->userID;
        if ($this->userAction == 'set')
        {
            $user = User::findorfail($user_id);
            $this->db_access->user_id = $user_id;
        }else{
            $user = User::findorfail($this->db_access->user_id);
        }

        $this->db_access->farm_location_id = $user->farm_location_id;
        $this->db_access->department_division_id = $user->department_division_id;
        $this->db_access->action = "NULL";
        $this->db_access->access = "," . implode(",", $this->temp_acc);
        $this->db_access->save();

        $log_entry = [
            $this->userAction,
            'accesses',
            (isset($this->db_access_old) ? $this->db_access_old : ''),
            $this->db_access,
        ];
        AC::logEntry($log_entry);

        session()->flash('success', 'User Access Has Been ' . ($this->userAction == 'set' ? 'Set' : 'Updated') . ' For ' . $this->userFullName);
        return redirect('/access');
    }


    /**
     * Renders the view for user access.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.access.user-access');
    }

}
