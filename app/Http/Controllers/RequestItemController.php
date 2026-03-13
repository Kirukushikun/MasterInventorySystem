<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestItem as RI;
use App\Models\ItemList as IL;
use App\Models\ItemName as ITNAME;
use App\Models\Category as CT;
use App\Models\SubCategory as SCT;
use App\Models\Product as PRD;
use App\Models\Approvals;
use App\Models\User;
use App\Models\UnitOfMeasurement as UOM;
use DataTables;
use Auth;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\GeneralController as GC;
use App\Http\Controllers\AuditController as AC;
use App\Http\Controllers\AccessController as ACC;
use App\Http\Controllers\WithdrawalSeriesController as WSC;

class RequestItemController extends Controller
{
    public function reqItems()
    {
        return view('request-item.request-item');
    }

    public function showReqItems($id)
    {
        return view('request-item.request-item-update', ['req_id' => GC::decryptString($id)]);
    }

    public function riList(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->buildDataTableData();

            return DataTables::of($data)
                ->rawColumns(['action', 'status', 'items_requested'])
                ->make(true);
        }

        return view('request-item.request-item-table');
    }

    private function buildDataTableData()
    {
        $user = User::findOrFail(Auth::id());
        $uomQuery = RI::where('active_status', 1);
        $uom = $this->getUOMData($user, $uomQuery);
        $data = collect();

        if ($uom->count() > 0) {
            foreach ($uom as $key => $al) {
                $itemLists = IL::where('request_item_id', $al->id)->where('active_status', 1)->get();
                $it_list = $this->buildItemListTable($itemLists, $al->id);

                $data->push([
                    'id' => $key + 1,
                    'series_number' => strtoupper($al->series_number),
                    'requested_by' => strtoupper(GC::getUserFullName($al->requested_by_id)),
                    'location' => strtoupper(WSC::getValueById($al->farm_location_id, "farm")),
                    'department_division' => strtoupper(WSC::getValueById($al->department_division_id, "dep")),
                    'status' => '<span class="badge badge-warning">' . strtoupper(Approvals::where('id', $al->approval_id)->first()->title) . '</span>',
                    'items_requested' => $it_list,
                    'date_requested' => $al->date_requested,
                    'date_needed' => $al->date_needed,
                    'action' => $this->buildActionButtons($al),
                ]);
            }
        }

        return $data;
    }

    private function getUOMData($user, $uomQuery)
    {
        if ($user->role == "superuser") {
            return $uomQuery->get();
        } elseif ($user->role == "user") {
            return $uomQuery->where('requested_by_id', Auth::id())->get();
        } else {
            return $uomQuery
                ->where('requested_by_id', $user->id)
                ->where('farm_location_id', $user->farm_location_id)
                ->where('department_division_id', $user->department_division_id)
                ->get();
        }
    }

    private function buildItemListTable($itemLists, $requestItemId)
    {
        $table_head = '<table class="table table-responsive table-hover text-wrap" style="width: 100%;">
            <thead>
                <tr>
                    <th><u>ITEM #</u></th>
                    <th><u>ITEM NAME</u></th>
                    <th><u>CATEGORY</u></th>
                    <th><u>SUB CATEGORY</u></th>
                    <th><u>PRODUCT</u></th>
                    <th><u>U/M</u></th>
                    <th><u>QUANTITY</u></th>
                </tr>
            </thead>
            <tbody>';

        $table_foot = '</tbody></table>';

        $it_list = '';
        foreach ($itemLists as $key => $il) {
            $item_name = ITNAME::findorfail($il->item_id);
            $it_list .= '
                <tr>
                    <td>' . ($key + 1) . '</td>
                    <td>' . (empty($item_name->item_name) ? "-" : strtoupper($item_name->item_name)) . '</td>
                    <td>' . (empty(CT::findorfail($item_name->category_id)->category_name) ? "-" : strtoupper(CT::findorfail($item_name->category_id)->category_name)) . '</td>
                    <td>' . (empty(SCT::findorfail($item_name->subcategory_id)->subcategory_name) ? "-" : strtoupper(SCT::findorfail($item_name->subcategory_id)->subcategory_name)) . '</td>
                    <td>' . (empty(PRD::findorfail($item_name->product_id)->product_name) ? "-" : strtoupper(PRD::findorfail($item_name->product_id)->product_name)) . '</td>
                    <td>' . (empty($il->uom_id) ? "-" : strtoupper(UOM::findorfail($il->uom_id)->abbreviation)) . '</td>
                    <td>' . (empty($il->item_quantity) ? "-" : $il->item_quantity) . '</td>
                </tr>';
        }

        $modal = '
            <button type="button" class="btn badge badge-primary" data-toggle="modal" data-target="#show' . $requestItemId . '">
                SHOW ITEM REQUESTED
            </button>
            <!-- Modal -->
            <div class="modal fade" id="show' . $requestItemId . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 80%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Item List</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            ' . $table_head . $it_list . $table_foot . '
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>';

        return $modal;
    }

    private function buildActionButtons($al)
    {
        $editButton = ACC::checkAccess(Auth::id(), 'withreq_edit')
            ? '<a href="' . route('request.item.show', ['id' => Crypt::encryptString($al->id)]) . '" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> UPDATE</a> '
            : '';

        $deleteButton = ACC::checkAccess(Auth::id(), 'withreq_del')
            ? '| <a href="' . route('delete.item', ['type' => 'RequestItem', 'id' => Crypt::encryptString($al->id)]) . '" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> DELETE</a>'
            : '';

        $disabledButton = (ACC::checkAccess(Auth::id(), 'withreq_edit') == false && ACC::checkAccess(Auth::id(), 'withreq_del') == false)
            ? '<a class="btn btn-info disabled">N/A</a>'
            : '';

        return $editButton . $deleteButton . $disabledButton;
    }
}