<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\{
	AuthenticationController,
	LoginController,
	DashboardController,
	AuditController,
	UsersController,
	AccessController,
	WithdrawalSeriesController,
    GatepassSeriesController,
	FarmLocationController,
    DepartmentDivisionController,
    RequestItemController,
    UnitOfMeasurementController,
    ApprovalsController,
    CategoryController,
    SubCategoryController,
    ProductController,
    ItemNameController,
    TransactionTypeController,
    LocationController,
    AlertTypeController,
    SupplierController,
    ItemController,
    FarmInventoryController,
    FarmStockCheckController,
    ForApprovalController,
    DeleteController,
    ReorderController,
};

Route::get('/item/details/{id?}', [ItemController::class, 'showItemDetailsDiv'])->name('item.div.details');
Route::get('/app-login/{id}', [AuthenticationController::class, 'app_login'])->name('app.login');
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/test', function () {
	$user = User::find(4); 
	Auth::login($user);

	return view('/dash');
})->name('test');

Route::middleware(['cenw', 'auth', 'cors'])->group(function() {


	Route::get('/', [DashboardController::class, 'dash'])->name('dash');

	Route::get('/audit', [AuditController::class, 'trail'])->name('audit');

	Route::get('/user', [UsersController::class, 'userJsonToDataTables'])->name('user');
	Route::get('/user/grant/access/{id}', [UsersController::class, 'usersGrantAccess'])->name('user.grant.access');

	Route::get('/delete/{type}/{id}', [DeleteController::class, 'delete'])->name('delete.item');

	Route::prefix('/withdrawal/series')->group(function () {
	    Route::get('/', [WithdrawalSeriesController::class, 'withrawalSeries'])->name('withrawal.series');
	    Route::get('/show/{id?}', [WithdrawalSeriesController::class, 'showWithrawalSeries'])->name('withrawal.series.show');
	    Route::get('/list', [WithdrawalSeriesController::class, 'wsList'])->name('withrawal.series.list');
	    Route::get('/list/{field_name?}/{field_value?}', [WithdrawalSeriesController::class, 'wsLocationOrDepartment'])->name('withrawal.series.locationordepartment');
	    Route::get('/list/all/ws/{from?}/{to?}/{field_location?}/{field_department?}', [WithdrawalSeriesController::class, 'wsAll'])->name('withrawal.series.all');
	    Route::get('/list/all/ws/rf', [WithdrawalSeriesController::class, 'wsRemoveFilter'])->name('withrawal.series.removefilter');
	});

	Route::prefix('/gatepass/series')->group(function () {
	    Route::get('/', [GatepassSeriesController::class, 'gatepassSeries'])->name('gatepass.series');
	    Route::get('/show/{id?}', [GatepassSeriesController::class, 'showGatepassSeries'])->name('gatepass.series.show');
	    Route::get('/list', [GatepassSeriesController::class, 'gsList'])->name('gatepass.series.list');
	    Route::get('/list/{field_name?}/{field_value?}', [GatepassSeriesController::class, 'gsLocationOrDepartment'])->name('gatepass.series.locationordepartment');
	    Route::get('/list/all/gs/{from?}/{to?}/{field_location?}/{field_department?}', [GatepassSeriesController::class, 'gsAll'])->name('gatepass.series.all');
	    Route::get('/list/all/gs/rf', [GatepassSeriesController::class, 'gsRemoveFilter'])->name('gatepass.series.removefilter');
	});


	Route::prefix('/request/item')->group(function () {
	    Route::get('/', [RequestItemController::class, 'reqItems'])->name('request.item');
	    Route::get('/show/{id?}', [RequestItemController::class, 'showReqItems'])->name('request.item.show');
	    Route::get('/list', [RequestItemController::class, 'riList'])->name('request.item.list');
	});

	Route::prefix('/farm/location')->group(function () {
	    Route::get('/', [FarmLocationController::class, 'farmLocation'])->name('farm.location');
	    Route::get('/show/{id?}', [FarmLocationController::class, 'showFarmLocation'])->name('farm.location.show');
	    Route::get('/list', [FarmLocationController::class, 'flList'])->name('farm.location.list');
	    Route::get('/list/{field_name?}/{field_value?}', [FarmLocationController::class, 'flLocationOrDepartment'])->name('farm.location.location');
	    Route::get('/list/all/fl/rf', [FarmLocationController::class, 'flRemoveFilter'])->name('farm.location.removefilter');
	});

	Route::prefix('/department')->group(function () {
	    Route::get('/', [DepartmentDivisionController::class, 'deptDiv'])->name('dept.div');
	    Route::get('/show/{id?}', [DepartmentDivisionController::class, 'showDeptDiv'])->name('dept.div.show');
	    Route::get('/list', [DepartmentDivisionController::class, 'ddList'])->name('department.list');
	    Route::get('/list/{field_name?}/{field_value?}', [DepartmentDivisionController::class, 'ddLocationOrDepartment'])->name('department.location');
	    Route::get('/list/all/dd/rf', [DepartmentDivisionController::class, 'ddRemoveFilter'])->name('department.removefilter');
	});

	Route::prefix('/uom')->group(function () {
	    Route::get('/', [UnitOfMeasurementController::class, 'uomDiv'])->name('uom.div');
	    Route::get('/show/{id?}', [UnitOfMeasurementController::class, 'showUomDiv'])->name('uom.div.show');
	    Route::get('/list', [UnitOfMeasurementController::class, 'uomList'])->name('uom.list');
	});

	Route::prefix('/approvals')->group(function () {
	    Route::get('/', [ApprovalsController::class, 'approvalsDiv'])->name('approvals.div');
	    Route::get('/show/{id?}', [ApprovalsController::class, 'showApprovalsDiv'])->name('approvals.div.show');
	    Route::get('/list', [ApprovalsController::class, 'approvalsList'])->name('approvals.list');
	});

	Route::prefix('/for/approval')->group(function () {
	    Route::get('/list', [ForApprovalController::class, 'forApprovalList'])->name('for.approval.list');
        Route::get('/farm/stock/check/{id?}', [FarmStockCheckController::class, 'showCheckFarmStock'])->name('farm.stock.check');
	});

	Route::prefix('/location')->group(function () {
	    Route::get('/', [LocationController::class, 'locationDiv'])->name('location.div');
	    Route::get('/show/{id?}', [LocationController::class, 'showLocationDiv'])->name('location.div.show');
	    Route::get('/list', [LocationController::class, 'locationList'])->name('location.list');
	});

	Route::prefix('/category')->group(function () {

	    Route::get('/', [CategoryController::class, 'categoryDiv'])->name('category.div');

	    Route::get('/show/{id?}', [CategoryController::class, 'showCategoryDiv'])->name('category.div.show');
	    Route::get('/list', [CategoryController::class, 'categoryList'])->name('category.list');
	});

	Route::prefix('/subcategory')->group(function () {
	    Route::get('/', [SubCategoryController::class, 'subcategoryDiv'])->name('subcategory.div');
	    Route::get('/show/{id?}', [SubCategoryController::class, 'showSubCategoryDiv'])->name('subcategory.div.show');
	    Route::get('/list', [SubCategoryController::class, 'subcategoryList'])->name('subcategory.list');
	});

	Route::prefix('/product')->group(function () {
	    Route::get('/', [ProductController::class, 'productDiv'])->name('product.div');
	    Route::get('/show/{id?}', [ProductController::class, 'showProductDiv'])->name('product.div.show');
	    Route::get('/list', [ProductController::class, 'productList'])->name('product.list');
	});
	Route::prefix('/itemname')->group(function () {
	    Route::get('/', [ItemNameController::class, 'itemnameDiv'])->name('itemname.div');
	    Route::get('/show/{id?}', [ItemNameController::class, 'showItemNameDiv'])->name('itemname.div.show');
	    Route::get('/list', [ItemNameController::class, 'itemnameList'])->name('itemname.list');
	});

	Route::prefix('/supplier')->group(function () {
	    Route::get('/', [SupplierController::class, 'supplierDiv'])->name('supplier.div');
	    Route::get('/show/{id?}', [SupplierController::class, 'showSupplierDiv'])->name('supplier.div.show');
	    Route::get('/list', [SupplierController::class, 'supplierList'])->name('supplier.list');
	});

	Route::prefix('/item')->group(function () {
	    Route::get('/', [ItemController::class, 'itemDiv'])->name('item.div');
	    Route::get('/show/{id?}', [ItemController::class, 'showItemDiv'])->name('item.div.show');
	    Route::get('/list', [ItemController::class, 'itemList'])->name('item.list');
	    // Route::get('/details/{id?}', [ItemController::class, 'showItemDetailsDiv'])->name('item.div.details');
	    Route::get('/checkout/{id?}', [ItemController::class, 'showItemCheckoutDiv'])->name('item.div.checkout');
	    Route::get('/multiple/checkout/{ids?}', [ItemController::class, 'showItemMultipleCheckoutDiv'])->name('item.div.multiple.checkout');
	});

	Route::prefix('/item/reorder')->group(function () {
	    Route::get('/show/{id?}', [ReorderController::class, 'showReorderDiv'])->name('reorder.div.show');
	    Route::get('/list', [ReorderController::class, 'reorderList'])->name('reorder.list');
	});

	Route::prefix('/farmitem')->group(function () {
	    Route::get('/', [FarmInventoryController::class, 'farmitemDiv'])->name('farmitem.div');
	    Route::get('/show/{id?}', [FarmInventoryController::class, 'showFarmItemDiv'])->name('farmitem.div.show');
	    Route::get('/list', [FarmInventoryController::class, 'farmItemList'])->name('farmitem.list');
	    Route::get('/details/{id?}', [FarmInventoryController::class, 'showFarmItemDetailsDiv'])->name('farmitem.div.details');
	});

	Route::prefix('/transaction/type')->group(function () {
	    Route::get('/', [TransactionTypeController::class, 'transactionTypeDiv'])->name('transaction.type.div');
	    Route::get('/show/{id?}', [TransactionTypeController::class, 'showTransactionTypeDiv'])->name('transaction.type.div.show');
	    Route::get('/list', [TransactionTypeController::class, 'transactionTypeList'])->name('transaction.type.list');
	});

	Route::prefix('/alert/type')->group(function () {
	    Route::get('/', [AlertTypeController::class, 'alertTypeDiv'])->name('alert.type.div');
	    Route::get('/show/{id?}', [AlertTypeController::class, 'showAlertTypeDiv'])->name('alert.type.div.show');
	    Route::get('/list', [AlertTypeController::class, 'alertTypeList'])->name('alert.type.list');
	});

	// Route For Access
	Route::get('/access', [AccessController::class, 'farmAccess'])->name('access');
	Route::get('/access/set/{id}/{fullname}/{action}', [AccessController::class, 'set_acc'])->name('access.set_acc');
});
