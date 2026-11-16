<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Display items list.
     * User: Read Only | Admin: Role Full
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = Item::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('item_code', 'like', "%{$search}%")
                      ->orWhere('item_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->withCount('salesLines')
            ->orderBy('item_code');

        $items = $query->paginate(10)->withQueryString();
        $categories = Item::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('items.index', compact('items', 'search', 'category', 'categories'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถเพิ่มสินค้าได้ (Item Master: Admin Only)');
        }

        $lastItem = Item::orderBy('item_id', 'desc')->first();
        $nextNum = $lastItem ? ($lastItem->item_id + 1) : 1;
        $suggestedCode = 'PROD-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $categories = Item::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('items.create', compact('suggestedCode', 'categories'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถเพิ่มสินค้าได้ (Item Master: Admin Only)');
        }

        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:20', 'unique:Items,item_code'],
            'item_name' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:20'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'item_code.required' => 'กรุณาระบุรหัสสินค้า',
            'item_code.unique' => 'รหัสสินค้านี้มีอยู่ในระบบแล้ว',
            'item_name.required' => 'กรุณาระบุชื่อสินค้า',
            'unit_price.required' => 'กรุณาระบุราคาต่อหน่วย',
            'unit_price.numeric' => 'ราคาต่อหน่วยต้องเป็นตัวเลข',
            'unit_price.min' => 'ราคาต่อหน่วยต้องมากกว่าหรือเท่ากับ 0 (Price >= 0)',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'เพิ่มสินค้าใหม่เรียบร้อยแล้ว');
    }

    public function edit(Item $item)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถแก้ไขสินค้าได้ (Item Master: Admin Only)');
        }

        $categories = Item::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถแก้ไขสินค้าได้ (Item Master: Admin Only)');
        }

        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:20', 'unique:Items,item_code,' . $item->item_id . ',item_id'],
            'item_name' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:20'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'item_code.required' => 'กรุณาระบุรหัสสินค้า',
            'item_code.unique' => 'รหัสสินค้านี้มีอยู่ในระบบแล้ว',
            'item_name.required' => 'กรุณาระบุชื่อสินค้า',
            'unit_price.required' => 'กรุณาระบุราคาต่อหน่วย',
            'unit_price.min' => 'ราคาต่อหน่วยต้องมากกว่าหรือเท่ากับ 0 (Price >= 0)',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'ปรับปรุงข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    public function destroy(Item $item)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถลบสินค้าได้ (Item Master: Admin Only)');
        }

        if ($item->salesLines()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบสินค้านี้ได้ เนื่องจากมีประวัติอยู่ในรายการใบสั่งขาย');
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }

    /**
     * API to return item info for AJAX dynamic dropdown & Auto-fill
     */
    public function getItemJson(Item $item)
    {
        return response()->json([
            'item_id' => $item->item_id,
            'item_code' => $item->item_code,
            'item_name' => $item->item_name,
            'category' => $item->category ?? '',
            'unit' => $item->unit ?? '',
            'unit_price' => (float)$item->unit_price,
            'description' => $item->description ?? '',
        ]);
    }
}
