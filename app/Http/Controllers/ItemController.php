<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = Item::query()
            ->when($search, function ($query, $search) {
                $query->where('item_code', 'like', "%{$search}%")
                      ->orWhere('item_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->withCount('salesLines')
            ->orderBy('item_code')
            ->paginate(10)
            ->withQueryString();

        return view('items.index', compact('items', 'search'));
    }

    public function create()
    {
        $lastItem = Item::orderBy('item_id', 'desc')->first();
        $nextNum = $lastItem ? ($lastItem->item_id + 1) : 1;
        $suggestedCode = 'ITEM-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('items.create', compact('suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:20', 'unique:Items,item_code'],
            'item_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:20'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'item_code.required' => 'กรุณาระบุรหัสสินค้า',
            'item_code.unique' => 'รหัสสินค้านี้มีอยู่ในระบบแล้ว',
            'item_name.required' => 'กรุณาระบุชื่อสินค้า',
            'unit_price.required' => 'กรุณาระบุราคาต่อหน่วย',
            'unit_price.numeric' => 'ราคาต่อหน่วยต้องเป็นตัวเลข',
            'unit_price.min' => 'ราคาต่อหน่วยต้องไม่ต่ำกว่า 0',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'เพิ่มสินค้าใหม่เรียบร้อยแล้ว');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:20', 'unique:Items,item_code,' . $item->item_id . ',item_id'],
            'item_name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:20'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'item_code.required' => 'กรุณาระบุรหัสสินค้า',
            'item_code.unique' => 'รหัสสินค้านี้มีอยู่ในระบบแล้ว',
            'item_name.required' => 'กรุณาระบุชื่อสินค้า',
            'unit_price.required' => 'กรุณาระบุราคาต่อหน่วย',
            'unit_price.numeric' => 'ราคาต่อหน่วยต้องเป็นตัวเลข',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'ปรับปรุงข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    public function destroy(Item $item)
    {
        if ($item->salesLines()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบสินค้านี้ได้ เนื่องจากมีประวัติอยู่ในรายการใบสั่งขาย');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }

    /**
     * API to return item info for AJAX dynamic dropdown
     */
    public function getItemJson(Item $item)
    {
        return response()->json([
            'item_id' => $item->item_id,
            'item_code' => $item->item_code,
            'item_name' => $item->item_name,
            'unit' => $item->unit ?? '',
            'unit_price' => (float)$item->unit_price,
            'description' => $item->description ?? '',
        ]);
    }
}

