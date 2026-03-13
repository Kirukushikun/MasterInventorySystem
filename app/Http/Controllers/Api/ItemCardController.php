<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemCardController extends Controller
{
    // Get all items
    public function index(Request $request)
    {
        $items = Item::with([
            'category',
            'subcategory',
            'product',
            'itemName',
            'location',
            'uom',
            'supplier'
        ])->get();

        $data = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'item_number' => $item->item_number,
                'category' => $item->category->category_name ?? null,
                'subcategory' => $item->subcategory->subcategory_name ?? null,
                'product' => $item->product->product_name ?? null,
                'item_name' => $item->itemName->item_name ?? null,
                'location' => $item->location->location_name ?? null,
                'quantity' => $item->quantity,
                'uom' => $item->uom->uom_name ?? null,
                'status' => $item->active_status ? 'in stock' : 'out of stock',
                'reorder_threshold' => $item->reorder_threshold,
                'purchase_date' => $item->purchase_date,
                'expiry_date' => $item->expiry_date,
                'remarks' => $item->remarks,
            ];
        });

        return response()->json($data);
    }

    // Get item by ID or item_number
    public function show($id)
    {
        $item = Item::with([
            'category',
            'subcategory',
            'product',
            'itemName',
            'location',
            'uom',
            'supplier'
        ])->where('id', $id)
          ->orWhere('item_number', $id)
          ->first();

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $data = [
            'id' => $item->id,
            'item_number' => $item->item_number,
            'category' => $item->category->category_name ?? null,
            'subcategory' => $item->subcategory->subcategory_name ?? null,
            'product' => $item->product->product_name ?? null,
            'item_name' => $item->itemName->item_name ?? null,
            'location' => $item->location->location_name ?? null,
            'quantity' => $item->quantity,
            'uom' => $item->uom->uom_name ?? null,
            'status' => $item->active_status ? 'in stock' : 'out of stock',
            'reorder_threshold' => $item->reorder_threshold,
            'purchase_date' => $item->purchase_date,
            'expiry_date' => $item->expiry_date,
            'remarks' => $item->remarks,
        ];

        return response()->json($data);
    }
}

