<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;

use App\Models\Item;
use App\Models\ItemName;
use App\Models\User;
use App\Models\Approvals;
use App\Models\Category;
use App\Models\FrequenVisit;
use App\Models\Location;
use App\Models\FarmLocation;
use App\Models\DepartmentDivision;
use App\Models\InventoryHistory;
use App\Models\Transaction;
use App\Models\RequestItem as RI;

use Carbon\Carbon;

class Dashboard extends Component
{
    public
        $total_items,
        $low_stock_items,
        $out_of_stock_items,
        $most_stock_items,
        $total_visit,
        $recent_transactions,
        $item_categories,
        $item_assigned_per_farm,
        $fastMovingItems,
        $slowMovingItems,
        $abundant_level = 0,
        $sufficient_level = 0,
        $warning_level = 0,
        $crtical_level = 0,
        $out_of_stoock_level = 0,
        $count_of_intransit = 0,
        $count_of_approved = 0,
        $count_of_denied = 0,
        $count_of_for_release = 0,
        $count_of_received = 0,
        $count_of_checked_out = 0,
        $reorder_not_set = 0,
        $routes_array;

    public function mount()
    {
        // Total items
        $this->total_items = Item::where('active_status', 1)->count();

        // Low stock items
        $this->low_stock_items = Item::where('current_quantity', '>', 0)
            ->where('current_quantity', '<', 11)
            ->where('active_status', 1)
            ->count();

        // Out of stock items
        $this->out_of_stock_items = Item::where('current_quantity', '<', 1)
            ->where('active_status', 1)
            ->count();

        // Most stock items
        $this->most_stock_items = Item::where('current_quantity', '>', 10)
            ->where('active_status', 1)
            ->count();

        // Recent transactions
        $recent_transactions = Transaction::where('active_status', 1)
            ->with(['issuedBy', 'issuedTo', 'item'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $this->recent_transactions = $recent_transactions->map(function ($rt, $index) {
            return [
                'number' => $index + 1,
                'date' => $rt->created_at->format('D M j, Y g:iA'),
                'issued_by' => $rt->issuedBy->name,
                'issued_to' => $rt->issuedTo->name,
                'item' => strtoupper($rt->item->item_name),
            ];
        });

        // Total visits
        $this->total_visits = FrequenVisit::sum('visit_count');

        // Item categories
        $item_categories = Category::where('active_status', 1)->withCount('items')->get();

        $this->item_categories = $item_categories->map(function ($ic, $index) {
            return [
                'number' => $index + 1,
                'cat_name' => $ic->category_name,
                'item_count' => $ic->items_count,
            ];
        });


        // $sumOfVisitCount = FrequenVisit::sum('visit_count');
        // $this->total_visits = $sumOfVisitCount;

        // Fetch all farm locations with related transactions
        $farmLocations = FarmLocation::where('active_status', 1)->with('transactions')->get();

        // Initialize arrays
        $this->item_assigned_per_farm = [];
        $ctr = 1;

        foreach ($farmLocations as $farmLocation) {
            // Check if there are transactions for the farm location
            if ($farmLocation->transactions->isNotEmpty()) {
                foreach ($farmLocation->transactions as $transaction) {
                    try {
                        $item = Item::findOrFail($transaction->item_id)->item_name;
                        $count_of_item = $transaction->quantity ?? 0;
                    } catch (ModelNotFoundException $e) {
                        $item = "N/A";
                        $count_of_item = 0;
                    }

                    $this->item_assigned_per_farm[] = [
                        'number' => $ctr,
                        'farm_location' => $farmLocation->farm_location,
                        'department_division' => DepartmentDivision::findOrFail($transaction->department_division_id)->department_division,
                        'item' => $item,
                        'item_count' => $count_of_item,
                    ];
                    $ctr++;
                }
            } else {
                // Handle case where there are no transactions for the farm location
                $this->item_assigned_per_farm[] = [
                    'number' => $ctr,
                    'farm_location' => $farmLocation->farm_location,
                    'department_division' => "N/A",
                    'item' => "N/A",
                    'item_count' => 0,
                ];
                $ctr++;
            }
        }

        // Calculate turnover rate for each item
        // $items = Item::where('active_status', 1)->get();

        // $turnoverRate = [];

        // foreach ($items as $item) {
        //     $totalQuantitySold = $item->transactions()
        //         ->whereIn('transaction_type_id', [19, 18, 13, 11]) // Replace with your transaction type filter
        //         ->where('transaction_date', '>=', now()->subDay(30)->format('Y-m-d H:i:s'))
        //         ->sum('quantity');

        //     $turnoverRate[] = [
        //         'item_name' => $item->item_name,
        //         'quantity_consumed' => $totalQuantitySold,
        //     ];
        // }

        // // Sort items by quantity_consumed in descending order
        // usort($turnoverRate, function ($a, $b) {
        //     return $b['quantity_consumed'] - $a['quantity_consumed'];
        // });

        // // Display top 5 fast-moving and slow-moving items
        // $this->fastMovingItems = array_slice($turnoverRate, 0, 5);
        // $this->slowMovingItems = array_slice($turnoverRate, -5);

        // usort($this->slowMovingItems, function ($a, $b) {
        //     return $a['quantity_consumed'] - $b['quantity_consumed'];
        // });

        // Calculate turnover rate for each item
        $items = Item::where('active_status', 1)->get(); // Retrieve all items from the inventory table
        $turnoverRate = [];

        foreach ($items as $item) {
            // Assuming you have a unique identifier for each item (e.g., $item->id)
            $id = $item->id;

            $totalQuantitySold = Transaction::where('item_id', $id)
                ->whereIn('transaction_type_id', [19, 18, 13, 11]) // Replace with your transaction type filter
                ->where('transaction_date', '>=', now()->subDay(30)->format('Y-m-d H:i:s')) // Adjust the date filter
                ->sum('quantity');

            $turnoverRate[] = [
                'item_name' => ItemName::findOrFail($item->item_name_id)->item_name,
                'quantity_consumed' => $totalQuantitySold,
            ];
        }

        // Sort items by quantity_consumed in descending order
        usort($turnoverRate, function ($a, $b) {
            return $b['quantity_consumed'] - $a['quantity_consumed'];
        });

        // Display top 5 fast-moving and slow-moving items
        $this->fastMovingItems = array_slice($turnoverRate, 0, 5);

        $this->slowMovingItems = array_slice($turnoverRate, -5);
        usort($this->slowMovingItems, function ($a, $b) {
            return $a['quantity_consumed'] - $b['quantity_consumed'];
        });




        // Fetch only necessary columns to reduce memory usage
        $itemData = Item::where('active_status', 1)->get(['quantity', 'reorder_threshold']);

        // Initialize counters
        $this->abundant_level = 0;
        $this->sufficient_level = 0;
        $this->warning_level = 0;
        $this->crtical_level = 0;
        $this->out_of_stoock_level = 0;
        $this->reorder_not_set = 0;

        foreach ($itemData as $item) {
            $excess = (int) $item->quantity - (int) $item->reorder_threshold;
            $perc = 0;

            // Calculate percentage if reorder_threshold is not zero
            if ($item->reorder_threshold > 0) {
                $perc = round($excess / (int) $item->reorder_threshold * 100, 0);
            }

            // Increment counters based on percentage levels
            if ($perc >= 50) {
                $this->abundant_level++;
            } elseif ($perc >= 10) {
                $this->sufficient_level++;
            } elseif ($perc >= 0) {
                $this->warning_level++;
            } elseif ($perc >= -99) {
                $this->crtical_level++;
            } else {
                $this->out_of_stoock_level++;
            }

            // Count items with reorder_threshold set to 0
            if ($item->reorder_threshold == 0) {
                $this->reorder_not_set++;
            }
        }


        // Retrieve all approvals with their titles
        $approvals = Approvals::where('active_status', 1)
        ->whereIn('title', ['In-Transit', 'IN-TRANSIT', 'in-transit', 'DENIED', 'denied', 'For Release', 'FOR RELEASE', 'for release', 'Received', 'RECEIVED', 'received', 'Item Checked Out', 'ITEM CHECKED OUT', 'item checked out'])
        ->pluck('id', 'title');

        // Retrieve counts for each approval title
        $this->count_of_intransit = $approvals->has('In-Transit') ? RI::where('active_status', 1)->where('approval_id', $approvals['In-Transit'])->count() : 0;
        $this->count_of_approved = $approvals->filter(function ($value, $key) {
        return in_array($key, ['In-Transit', 'IN-TRANSIT', 'in-transit']);
        })->map(function ($value) {
        return RI::where('active_status', 1)->where('approval_id', $value)->count();
        })->sum();
        $this->count_of_denied = $approvals->has('DENIED') ? RI::where('active_status', 1)->where('approval_id', $approvals['DENIED'])->count() : 0;
        $this->count_of_for_release = $approvals->has('For Release') ? RI::where('active_status', 1)->where('approval_id', $approvals['For Release'])->count() : 0;
        $this->count_of_received = $approvals->has('Received') ? RI::where('active_status', 1)->where('approval_id', $approvals['Received'])->count() : 0;
        $this->count_of_checked_out = $approvals->has('Item Checked Out') ? RI::where('active_status', 1)->where('approval_id', $approvals['Item Checked Out'])->count() : 0;

        // Overall count of requests
        $this->overall_request = RI::where('active_status', 1)->count();

    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
