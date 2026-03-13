<?php

namespace App\Http\Livewire\Navigation;

use Livewire\Component;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;
use Auth;
use App\Http\Controllers\AccessController as AC;
use App\Http\Controllers\GeneralController as GC;
use App\Models\Access;

class SideNav extends Component
{
    public $route_title;
    public $access;

    public function mount()
    {

        $user_access = Access::where('user_id', Auth::id())->first();
        $access = $user_access->access;

        $accesses_array = explode(',', $access);
        $accesses = array_filter($accesses_array);

        $this->access = $accesses;

        $access_list = [
            // ── Overview ──
            'dash_mod'         => ['section' => 'Overview',       'title' => 'Dashboard',           'route' => 'dash',                 'icon' => 'bi bi-grid'],

            // ── Inventory ──
            'inventory_add'    => ['section' => 'Inventory',      'title' => 'Inventory',           'route' => 'item.div',             'icon' => 'bi bi-box-seam'],
            'farminventory_add'=> ['section' => 'Inventory',      'title' => 'Farm Inventory',      'route' => 'farmitem.list',        'icon' => 'bi bi-flower1'],
            'cat_add'          => ['section' => 'Inventory',      'title' => 'Category',            'route' => 'category.div',         'icon' => 'bi bi-layers'],
            'subcat_add'       => ['section' => 'Inventory',      'title' => 'SubCategory',         'route' => 'subcategory.div',      'icon' => 'bi bi-list-ul'],
            'product_add'      => ['section' => 'Inventory',      'title' => 'Product',             'route' => 'product.div',          'icon' => 'bi bi-tag'],
            'itemname_add'     => ['section' => 'Inventory',      'title' => 'Items',               'route' => 'itemname.div',         'icon' => 'bi bi-file-text'],

            // ── Operations ──
            'reorder_mod'      => ['section' => 'Operations',     'title' => 'Re-Order',            'route' => 'reorder.list',         'icon' => 'bi bi-envelope'],
            'location_add'     => ['section' => 'Operations',     'title' => 'Bin Location',        'route' => 'location.div',         'icon' => 'bi bi-upc-scan'],
            'withreq_add'      => ['section' => 'Operations',     'title' => 'Request Supply',      'route' => 'request.item',         'icon' => 'bi bi-person-plus'],
            'withser_add'      => ['section' => 'Operations',     'title' => 'Withdrawal Series',   'route' => 'withrawal.series',     'icon' => 'bi bi-truck'],
            'gateser_add'      => ['section' => 'Operations',     'title' => 'Cole Plus Trans.',    'route' => 'gatepass.series',      'icon' => 'bi bi-check-square'],
            'approvals_add'    => ['section' => 'Operations',     'title' => 'Approval Statuses',   'route' => 'approvals.div',        'icon' => 'bi bi-check-circle'],
            'forapprovals_mod' => ['section' => 'Operations',     'title' => 'Manage Requests',     'route' => 'for.approval.list',    'icon' => 'bi bi-book'],

            // ── Administration ──
            'supplier_add'     => ['section' => 'Administration', 'title' => 'Manage Supplier',     'route' => 'supplier.div',         'icon' => 'bi bi-truck'],
            'farmloc_add'      => ['section' => 'Administration', 'title' => 'Farm Location',       'route' => 'farm.location',        'icon' => 'bi bi-geo-alt'],
            'deptdiv_add'      => ['section' => 'Administration', 'title' => 'Department/Division', 'route' => 'dept.div',             'icon' => 'bi bi-building'],
            'users_mod'        => ['section' => 'Administration', 'title' => 'Manage Users',        'route' => 'user',                 'icon' => 'bi bi-people'],
            'transtype_add'    => ['section' => 'Administration', 'title' => 'Transaction Type',    'route' => 'transaction.type.div', 'icon' => 'bi bi-arrow-left-right'],
            'alerttype_add'    => ['section' => 'Administration', 'title' => 'Notification Type',   'route' => 'alert.type.div',       'icon' => 'bi bi-bell'],
            'uom_add'          => ['section' => 'Administration', 'title' => 'UOM',                 'route' => 'uom.div',              'icon' => 'bi bi-rulers'],
            'access_mod'       => ['section' => 'Administration', 'title' => 'Access',              'route' => 'access',               'icon' => 'bi bi-key'],
            'audit_mod'        => ['section' => 'Administration', 'title' => 'Audit Trail',         'route' => 'audit',                'icon' => 'bi bi-pencil'],
        ];

        $this->route_title = []; // Initialize the route_title array

        // foreach ($accesses as $value) {
        //     if (array_key_exists($value, $access_list)) {
        //         $this->route_title[$value] = $access_list[$value];
        //     }
        // }

        foreach ($access_list as $key => $access) {
            if (in_array($key, $this->access)) {
                $this->route_title[$key] = $access_list[$key];
            }
        }
    }

    public function render()
    {
        return view('livewire.navigation.side-nav');
    }
}
