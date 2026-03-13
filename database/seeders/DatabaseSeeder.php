<?php

namespace Database\Seeders;

use App\Models\WithdrawalSeries;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\ItemName;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Location;
use App\Models\UnitOfMeasurement;
use App\Models\Approvals;
use App\Models\User;
use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

         \App\Models\DepartmentDivision::insert([
            [
                'id' => 1,
                'department_division' => 'PURCHASING DEPARTMENT',
                'abbreviation' => 'PURCH',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-06 18:58:24',
                'updated_at' => '2023-08-06 18:58:24',
            ],
            [
                'id' => 2,
                'department_division' => 'INFORMATION TECHNOLOGY AND SERCURITY SERVICES DEPARTMENT',
                'abbreviation' => 'ITSSD',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-06 19:00:13',
                'updated_at' => '2023-08-06 19:00:13',
            ],
        ]);

         \App\Models\FarmLocation::insert([
            [
                'id' => 1,
                'farm_location' => 'Brookside Farms',
                'abbreviation' => 'BFC',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-06 18:37:39',
                'updated_at' => '2023-08-06 18:37:39',
            ],
            [
                'id' => 2,
                'farm_location' => 'Poultrypure Farms',
                'abbreviation' => 'PFC',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-06 18:37:56',
                'updated_at' => '2023-08-06 18:37:56',
            ],
            [
                'id' => 3,
                'farm_location' => 'Brookdale Farms',
                'abbreviation' => 'BDL',
                'active_status' => 1,
                'deleted_status' => 0,
                'created_at' => '2023-08-06 18:38:16',
                'updated_at' => '2023-08-06 18:38:16',
            ],
        ]);

        User::insert([
            [
                'id' => 1,
                'name' => 'Michael Adam',
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'password' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'role' => 'superuser',
                'created_at' => '2023-07-31 14:14:33',
                'updated_at' => '2023-07-31 14:14:33',
            ],
            [
                'id' => 2,
                'name' => 'Kim Bacani',
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'password' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'role' => 'superuser',
                'created_at' => '2023-08-03 22:21:40',
                'updated_at' => '2023-08-03 22:21:40',
            ],
            [
                'id' => 3,
                'name' => 'David Toribio',
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'password' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'role' => 'superuser',
                'created_at' => '2023-08-03 22:21:40',
                'updated_at' => '2023-08-03 22:21:40',
            ],
            [
                'id' => 4,
                'name' => 'Rodgel Dantes',
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'password' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'role' => 'superuser',
                'created_at' => '2023-08-03 22:21:40',
                'updated_at' => '2023-08-03 22:21:40',
            ],
            [
                'id' => 5,
                'name' => 'Jeffrey Montiano',
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'password' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'role' => 'superuser',
                'created_at' => '2023-08-03 22:21:40',
                'updated_at' => '2023-08-03 22:21:40',
            ],
            [
                'id' => 6,
                'name' => 'Reneliza Yusi',
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'password' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'role' => 'superuser',
                'created_at' => '2023-08-03 22:21:40',
                'updated_at' => '2023-08-03 22:21:40',
            ],
            [
                'id' => 7,
                'name' => 'Adam Trinidad',
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'password' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'role' => 'superuser',
                'created_at' => '2023-08-03 22:21:40',
                'updated_at' => '2023-08-03 22:21:40',
            ],
            [
                'id' => 55,
                'name' => 'Approver Sample',
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'password' => NULL,
                'active_status' => 1,
                'deleted_status' => 0,
                'role' => 'approver',
                'created_at' => '2023-08-03 22:21:40',
                'updated_at' => '2023-08-03 22:21:40',
            ],

        ]);

        // Create Category-related data
        Category::factory()
            ->count(10)
            ->has(
                SubCategory::factory()
                    ->count(8)
            )
            ->create();

        // After creating all hierarchical models, create Items
        // (Assumes your factories are linking valid foreign keys)

        // Create base supporting data first
        UnitOfMeasurement::factory()->count(10)->create();
        Location::factory()->count(10)->create();
        Supplier::factory()->count(10)->create();
        $this->call(ApprovalsTableSeeder::class);

        Item::factory()->count(50)->create();

        $this->call(TransactionTypesTableSeeder::class);

        \App\Models\Access::insert([
            [
                'id' => 1,
                'user_id' => 1,
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'access' => ',dashboard_mod,audit_mod,access_mod',
                'created_at' => '2022-08-15 13:29:50',
                'updated_at' => '2023-08-01     16:28:40',
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'farm_location_id' => 1,
                'department_division_id' => 1,
                'access' => ',dash_mod,access_mod,access_set,alerttype_mod,alerttype_add,alerttype_edit,alerttype_del,approvals_mod,approvals_add,approvals_edit,approvals_del,audit_mod,cat_mod,cat_add,cat_edit,cat_del,deptdiv_mod,deptdiv_add,deptdiv_edit,deptdiv_del,farmloc_mod,farmloc_add,farmloc_edit,farmloc_del,forapprovals_mod,forapprovals_approve,forapprovals_deny,inventory_mod,inventory_add,inventory_edit,inventory_del,inventory_checkout,inventory_details,inventory_multiple,itemname_mod,itemname_add,itemname_edit,itemname_del,location_mod,location_add,location_edit,location_del,product_mod,product_add,product_edit,product_del,subcat_mod,subcat_add,subcat_edit,subcat_del,supplier_mod,supplier_add,supplier_edit,supplier_del,transtype_mod,transtype_add,transtype_edit,transtype_del,uom_mod,uom_add,uom_edit,uom_del,users_mod,users_grant,withreq_mod,withreq_add,withreq_edit,withreq_del,withser_mod,withser_add,withser_edit,withser_del,farminventory_mod,farminventory_edit,farminventory_del,farminventory_details,farminventory_add,inventory_diminish,reorder_edit,forapprovals_checkout,forapprovals_forrelease,forapprovals_intransit,forapprovals_received,forapprovals_update_mod,forapprovals_update_deny,reorder_mod,forapprovals_update_approve',
                'created_at' => '2022-08-15 13:29:50',
                'updated_at' => '2023-08-01 16:28:20',
            ],
            [
                'id' => 4,
                'user_id' => 5,
                'farm_location_id' => 1,
                'department_division_id' => 2,
                'access' => ',dash_mod,access_mod,access_set,alerttype_mod,alerttype_add,alerttype_edit,alerttype_del,approvals_mod,approvals_add,approvals_edit,approvals_del,audit_mod,cat_mod,cat_add,cat_edit,cat_del,deptdiv_mod,deptdiv_add,deptdiv_edit,deptdiv_del,farmloc_mod,farmloc_add,farmloc_edit,farmloc_del,forapprovals_mod,forapprovals_approve,forapprovals_deny,inventory_mod,inventory_add,inventory_edit,inventory_del,inventory_checkout,inventory_details,inventory_multiple,itemname_mod,itemname_add,itemname_edit,itemname_del,location_mod,location_add,location_edit,location_del,product_mod,product_add,product_edit,product_del,subcat_mod,subcat_add,subcat_edit,subcat_del,supplier_mod,supplier_add,supplier_edit,supplier_del,transtype_mod,transtype_add,transtype_edit,transtype_del,uom_mod,uom_add,uom_edit,uom_del,users_mod,users_grant,withreq_mod,withreq_add,withreq_edit,withreq_del,withser_mod,withser_add,withser_edit,withser_del,farminventory_mod,farminventory_edit,farminventory_del,farminventory_details,farminventory_add,inventory_diminish,reorder_edit,forapprovals_checkout,forapprovals_forrelease,forapprovals_intransit,forapprovals_received,forapprovals_update_mod,forapprovals_update_deny,reorder_mod,forapprovals_update_approve',
                'created_at' => '2022-08-15 13:29:50',
                'updated_at' => '2023-08-01 16:28:20',
            ],
            [
                'id' => 5,
                'user_id' => 55,
                'farm_location_id' => 1,
                'department_division_id' => 2,
                'access' => ',dash_mod,access_mod,access_set,alerttype_mod,alerttype_add,alerttype_edit,alerttype_del,approvals_mod,approvals_add,approvals_edit,approvals_del,audit_mod,cat_mod,cat_add,cat_edit,cat_del,deptdiv_mod,deptdiv_add,deptdiv_edit,deptdiv_del,farmloc_mod,farmloc_add,farmloc_edit,farmloc_del,forapprovals_mod,forapprovals_approve,forapprovals_deny,inventory_mod,inventory_add,inventory_edit,inventory_del,inventory_checkout,inventory_details,inventory_multiple,itemname_mod,itemname_add,itemname_edit,itemname_del,location_mod,location_add,location_edit,location_del,product_mod,product_add,product_edit,product_del,subcat_mod,subcat_add,subcat_edit,subcat_del,supplier_mod,supplier_add,supplier_edit,supplier_del,transtype_mod,transtype_add,transtype_edit,transtype_del,uom_mod,uom_add,uom_edit,uom_del,users_mod,users_grant,withreq_mod,withreq_add,withreq_edit,withreq_del,withser_mod,withser_add,withser_edit,withser_del,farminventory_mod,farminventory_edit,farminventory_del,farminventory_details,farminventory_add,inventory_diminish,reorder_edit,forapprovals_checkout,forapprovals_forrelease,forapprovals_intransit,forapprovals_received,forapprovals_update_mod,forapprovals_update_deny,reorder_mod,forapprovals_update_approve',
                'created_at' => '2022-08-15 13:29:50',
                'updated_at' => '2023-08-01 16:28:20',
            ],
        ]);
    }
}
