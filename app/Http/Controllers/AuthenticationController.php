<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\FrequenVisit;
use App\Models\Access;
use App\Http\Controllers\GeneralController as GC;

class AuthenticationController extends Controller
{
    private array $allowedModules = [
        'dash_mod' => ['route_name' => 'dash', 'route_uri' => 'dash'],
        'cat_mod' => ['route_name' => 'category.list', 'route_uri' => 'category'],
        'cat_add' => ['route_name' => 'category.div', 'route_uri' => 'category'],
        'cat_edit' => ['route_name' => 'category.div.show', 'route_uri' => 'category/show'],
        'cat_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/Category'],
        'subcat_mod' => ['route_name' => 'subcategory.list', 'route_uri' => 'subcategory'],
        'subcat_add' => ['route_name' => 'subcategory.div', 'route_uri' => 'subcategory'],
        'subcat_edit' => ['route_name' => 'subcategory.div.show', 'route_uri' => 'subcategory/show'],
        'subcat_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/SubCategory'],
        'product_mod' => ['route_name' => 'product.list', 'route_uri' => 'product'],
        'product_add' => ['route_name' => 'product.div', 'route_uri' => 'product'],
        'product_edit' => ['route_name' => 'product.div.show', 'route_uri' => 'product/show'],
        'product_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/Product'],
        'inventory_mod' => ['route_name' => 'item.list', 'route_uri' => 'item'],
        'inventory_add' => ['route_name' => 'item.div', 'route_uri' => 'item'],
        'inventory_edit' => ['route_name' => 'item.div.show', 'route_uri' => 'item/show'],
        'inventory_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/Item'],
        'inventory_checkout' => ['route_name' => 'item.div.details', 'route_uri' => 'item/details'],
        'inventory_details' => ['route_name' => 'item.div.checkout', 'route_uri' => 'item/checkout'],
        'inventory_multiple' => ['route_name' => 'item.div.multiple.checkout', 'route_uri' => 'item/multiple/checkout'],
        'reorder_mod' => ['route_name' => 'reorder.list', 'route_uri' => 'reorder'],
        'reorder_edit' => ['route_name' => 'reorder.div.show', 'route_uri' => 'reorder/show'],
        'farminventory_mod' => ['route_name' => 'farmitem.list', 'route_uri' => 'farmitem'],
        'farminventory_add' => ['route_name' => 'farmitem.div', 'route_uri' => 'farmitem'],
        'farminventory_edit' => ['route_name' => 'farmitem.div.show', 'route_uri' => 'farmitem/show'],
        'farminventory_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/FarmItem'],
        'farminventory_details' => ['route_name' => 'farmitem.div.details', 'route_uri' => 'farmitem/details'],
        'itemname_mod' => ['route_name' => 'itemname.list', 'route_uri' => 'itemname'],
        'itemname_add' => ['route_name' => 'itemname.div', 'route_uri' => 'itemname'],
        'itemname_edit' => ['route_name' => 'itemname.div.show', 'route_uri' => 'itemname/show'],
        'itemname_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/ItemName'],
        'transtype_mod' => ['route_name' => 'transaction.type.list', 'route_uri' => 'transaction/type'],
        'transtype_add' => ['route_name' => 'transaction.type.div', 'route_uri' => 'transaction/type'],
        'transtype_edit' => ['route_name' => 'transaction.type.div.show', 'route_uri' => 'transaction/type/show'],
        'transtype_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/TransactionType'],
        'alerttype_mod' => ['route_name' => 'alert.type.list', 'route_uri' => 'alert/type'],
        'alerttype_add' => ['route_name' => 'alert.type.div', 'route_uri' => 'alert/type'],
        'alerttype_edit' => ['route_name' => 'alert.type.div.show', 'route_uri' => 'alert/type/show'],
        'alerttype_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/AlertType'],
        'location_mod' => ['route_name' => 'location.list', 'route_uri' => 'location'],
        'location_add' => ['route_name' => 'location.div', 'route_uri' => 'location'],
        'location_edit' => ['route_name' => 'location.div.show', 'route_uri' => 'location/show'],
        'location_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/Location'],
        'supplier_mod' => ['route_name' => 'supplier.list', 'route_uri' => 'supplier'],
        'supplier_add' => ['route_name' => 'supplier.div', 'route_uri' => 'supplier'],
        'supplier_edit' => ['route_name' => 'supplier.div.show', 'route_uri' => 'supplier/show'],
        'supplier_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/Supplier'],
        'withser_mod' => ['route_name' => 'withrawal.series.list', 'route_uri' => 'withdrawal/series'],
        'withser_add' => ['route_name' => 'withrawal.series', 'route_uri' => 'withdrawal/series'],
        'withser_edit' => ['route_name' => 'withrawal.series.show', 'route_uri' => 'withdrawal/series/show'],
        'withser_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/WithdrawalSeries'],
        'gateser_add' => ['route_name' => 'gatepass.series', 'route_uri' => 'gatepass/series'],
        'gateser_mod' => ['route_name' => 'gatepass.series.list', 'route_uri' => 'gatepass/series'],
        'gateser_edit' => ['route_name' => 'gatepass.series.show', 'route_uri' => 'gatepass/series/show'],
        'gateser_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/GatepassSeries'],
        'withreq_mod' => ['route_name' => 'request.item.list', 'route_uri' => 'request/item'],
        'withreq_add' => ['route_name' => 'request.item', 'route_uri' => 'request/item'],
        'withreq_edit' => ['route_name' => 'request.item.show', 'route_uri' => 'request/item/show'],
        'withreq_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/RequestItem'],
        'farmloc_mod' => ['route_name' => 'farm.location.list', 'route_uri' => 'farm/location'],
        'farmloc_add' => ['route_name' => 'farm.location', 'route_uri' => 'farm/location'],
        'farmloc_edit' => ['route_name' => 'farm.location.show', 'route_uri' => 'farm/location/show'],
        'farmloc_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/FarmLocation'],
        'deptdiv_mod' => ['route_name' => 'department.list', 'route_uri' => 'department'],
        'deptdiv_add' => ['route_name' => 'dept.div', 'route_uri' => 'department'],
        'deptdiv_edit' => ['route_name' => 'dept.div.show', 'route_uri' => 'department/show'],
        'deptdiv_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/DepartmentDivision'],
        'uom_mod' => ['route_name' => 'uom.list', 'route_uri' => 'uom'],
        'uom_add' => ['route_name' => 'uom.div', 'route_uri' => 'uom'],
        'uom_edit' => ['route_name' => 'uom.div.show', 'route_uri' => 'uom/show'],
        'uom_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/UnitOfMeasurement'],
        'approvals_mod' => ['route_name' => 'approvals.list', 'route_uri' => 'approvals'],
        'approvals_add' => ['route_name' => 'approvals.div', 'route_uri' => 'approvals'],
        'approvals_edit' => ['route_name' => 'approvals.div.show', 'route_uri' => 'approvals/show'],
        'approvals_del' => ['route_name' => 'delete.item', 'route_uri' => 'delete/Approvals'],
        'forapprovals_mod' => ['route_name' => 'for.approval.list', 'route_uri' => 'for/approval'],
        'farmcheckstock_mod' => ['route_name' => 'farm.stock.check', 'route_uri' => 'for/approval/farm/stock/check'],
        'users_mod' => ['route_name' => 'user', 'route_uri' => 'user'],
        'users_grant' => ['route_name' => 'user.grant.access', 'route_uri' => 'user/grant/access'],
        'access_mod' => ['route_name' => 'access', 'route_uri' => 'access'],
        'access_set' => ['route_name' => 'access.set_acc', 'route_uri' => 'access/set'],
        'audit_mod' => ['route_name' => 'audit', 'route_uri' => 'audit'],
        'logout' => ['route_name' => 'logout', 'route_uri' => 'logout'],
    ];

    public function app_login($id = null)
    {
        if (Auth::check()) {
            $accesses = array_filter(explode(',', Access::where('user_id', Auth::id())->value('access')));
            $accesses[] = 'logout';

            foreach ($this->allowedModules as $key => $mod) {
                if (in_array($key, $accesses)) {
                    return redirect()->route($mod['route_name']);
                }
            }
        }

        $id = GC::decryptString($id);
        $user = User::find($id);

        if ($user && Auth::loginUsingId($user->id)) {
            FrequenVisit::updateOrCreate(
                ['user_id' => $user->id],
                ['visit_count' => \DB::raw('visit_count + 1')]
            );

            $accesses = array_filter(explode(',', Access::where('user_id', $user->id)->value('access')));
            $accesses[] = 'logout';

            foreach ($this->allowedModules as $key => $mod) {
                if (in_array($key, $accesses)) {
                    return redirect()->route($mod['route_name']);
                }
            }
        }

        return abort(403, 'Unauthorized');
    }
}
