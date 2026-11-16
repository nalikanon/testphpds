<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\SalesLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $customerId = $request->input('customer_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = SalesOrder::with(['customer', 'creator'])
            ->when($search, function ($q, $search) {
                $q->where('order_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('customer_code', 'like', "%{$search}%");
                  });
            })
            ->when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($customerId, function ($q, $customerId) {
                $q->where('customer_id', $customerId);
            })
            ->when($dateFrom, function ($q, $dateFrom) {
                $q->whereDate('order_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q, $dateTo) {
                $q->whereDate('order_date', '<=', $dateTo);
            })
            ->orderBy('order_date', 'desc')
            ->orderBy('order_id', 'desc');

        $orders = $query->paginate(10)->withQueryString();
        $customers = Customer::orderBy('customer_name')->get();

        return view('orders.index', compact('orders', 'customers', 'search', 'status', 'customerId', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        $prefix = 'SO-' . date('Ym') . '-';
        $latest = SalesOrder::where('order_no', 'like', "{$prefix}%")
            ->orderBy('order_no', 'desc')
            ->first();

        if ($latest) {
            $num = (int) substr($latest->order_no, strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        $orderNo = $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);

        $customers = Customer::orderBy('customer_code')->get();
        $items = Item::orderBy('item_code')->get();

        return view('orders.create', compact('orderNo', 'customers', 'items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_no' => ['required', 'string', 'max:20', 'unique:Sales_Orders,order_no'],
            'customer_id' => ['required', 'exists:Customers,customer_id'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'remark' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:Items,item_id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.remark' => ['nullable', 'string', 'max:255'],
        ], [
            'order_no.required' => 'กรุณาระบุเลขที่ใบสั่งขาย',
            'order_no.unique' => 'เลขที่ใบสั่งขายนี้มีอยู่ในระบบแล้ว',
            'customer_id.required' => 'กรุณาเลือกลูกค้า',
            'order_date.required' => 'กรุณาระบุวันที่สั่งซื้อ',
            'items.required' => 'กรุณาเพิ่มรายการสินค้าอย่างน้อย 1 รายการ',
            'items.min' => 'กรุณาเพิ่มรายการสินค้าอย่างน้อย 1 รายการ',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $linesData = [];

            foreach ($request->items as $index => $row) {
                $qty = (float) $row['quantity'];
                $price = (float) $row['unit_price'];
                $lineDiscPct = (float) ($row['discount_percent'] ?? 0);

                $grossLine = $qty * $price;
                $lineDiscAmt = round($grossLine * ($lineDiscPct / 100), 2);
                $lineTotal = round($grossLine - $lineDiscAmt, 2);

                $totalAmount += $lineTotal;

                $linesData[] = [
                    'item_id' => $row['item_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount_percent' => $lineDiscPct,
                    'discount_amount' => $lineDiscAmt,
                    'line_total' => $lineTotal,
                    'remark' => $row['remark'] ?? '',
                    'line_no' => $index + 1,
                ];
            }

            $orderDiscPct = (float) ($request->discount_percent ?? 0);
            $orderDiscAmt = round($totalAmount * ($orderDiscPct / 100), 2);
            $netAmount = round($totalAmount - $orderDiscAmt, 2);

            $vatPercent = (float) ($request->vat_percent ?? 7.00);
            $vatAmount = round($netAmount * ($vatPercent / 100), 2);
            $grandTotal = round($netAmount + $vatAmount, 2);

            $order = SalesOrder::create([
                'order_no' => $request->order_no,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'total_amount' => $totalAmount,
                'discount_percent' => $orderDiscPct,
                'discount_amount' => $orderDiscAmt,
                'net_amount' => $netAmount,
                'vat_percent' => $vatPercent,
                'vat_amount' => $vatAmount,
                'grand_total' => $grandTotal,
                'status' => 'draft',
                'remark' => $request->remark,
                'created_by' => Auth::id(),
            ]);

            foreach ($linesData as $ld) {
                $ld['order_id'] = $order->order_id;
                SalesLine::create($ld);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'สร้างใบสั่งขายเลขที่ ' . $order->order_no . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage());
        }
    }

    public function show(SalesOrder $order)
    {
        $order->load(['customer', 'creator', 'lines.item']);
        return view('orders.show', compact('order'));
    }

    public function edit(SalesOrder $order)
    {
        if (!$order->isDraft()) {
            return redirect()->route('orders.show', $order)->with('error', 'สามารถแก้ไขได้เฉพาะใบสั่งขายที่มีสถานะ "แบบร่าง (Draft)" เท่านั้น');
        }

        $order->load(['customer', 'lines.item']);
        $customers = Customer::orderBy('customer_code')->get();
        $items = Item::orderBy('item_code')->get();

        return view('orders.edit', compact('order', 'customers', 'items'));
    }

    public function update(Request $request, SalesOrder $order)
    {
        if (!$order->isDraft()) {
            return redirect()->route('orders.show', $order)->with('error', 'สามารถแก้ไขได้เฉพาะใบสั่งขายที่มีสถานะ "แบบร่าง (Draft)" เท่านั้น');
        }

        $request->validate([
            'order_no' => ['required', 'string', 'max:20', 'unique:Sales_Orders,order_no,' . $order->order_id . ',order_id'],
            'customer_id' => ['required', 'exists:Customers,customer_id'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'remark' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:Items,item_id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.remark' => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $linesData = [];

            foreach ($request->items as $index => $row) {
                $qty = (float) $row['quantity'];
                $price = (float) $row['unit_price'];
                $lineDiscPct = (float) ($row['discount_percent'] ?? 0);

                $grossLine = $qty * $price;
                $lineDiscAmt = round($grossLine * ($lineDiscPct / 100), 2);
                $lineTotal = round($grossLine - $lineDiscAmt, 2);

                $totalAmount += $lineTotal;

                $linesData[] = [
                    'order_id' => $order->order_id,
                    'item_id' => $row['item_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount_percent' => $lineDiscPct,
                    'discount_amount' => $lineDiscAmt,
                    'line_total' => $lineTotal,
                    'remark' => $row['remark'] ?? '',
                    'line_no' => $index + 1,
                ];
            }

            $orderDiscPct = (float) ($request->discount_percent ?? 0);
            $orderDiscAmt = round($totalAmount * ($orderDiscPct / 100), 2);
            $netAmount = round($totalAmount - $orderDiscAmt, 2);

            $vatPercent = (float) ($request->vat_percent ?? 7.00);
            $vatAmount = round($netAmount * ($vatPercent / 100), 2);
            $grandTotal = round($netAmount + $vatAmount, 2);

            $order->update([
                'order_no' => $request->order_no,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'total_amount' => $totalAmount,
                'discount_percent' => $orderDiscPct,
                'discount_amount' => $orderDiscAmt,
                'net_amount' => $netAmount,
                'vat_percent' => $vatPercent,
                'vat_amount' => $vatAmount,
                'grand_total' => $grandTotal,
                'remark' => $request->remark,
            ]);

            $order->lines()->delete();
            foreach ($linesData as $ld) {
                SalesLine::create($ld);
            }

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'ปรับปรุงใบสั่งขายเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage());
        }
    }

    public function confirm(SalesOrder $order)
    {
        if (!$order->isDraft()) {
            return back()->with('error', 'สามารถยืนยันได้เฉพาะใบสั่งขายสถานะ "แบบร่าง" เท่านั้น');
        }

        $order->status = 'confirmed';
        $order->save();

        return back()->with('success', 'ยืนยันใบสั่งขายเลขที่ ' . $order->order_no . ' สำเร็จแล้ว');
    }

    public function cancel(SalesOrder $order)
    {
        if ($order->isCancelled()) {
            return back()->with('error', 'ใบสั่งขายนี้ถูกยกเลิกแล้ว');
        }

        $order->status = 'cancelled';
        $order->save();

        return back()->with('success', 'ยกเลิกใบสั่งขายเลขที่ ' . $order->order_no . ' สำเร็จแล้ว');
    }

    public function destroy(SalesOrder $order)
    {
        if (!Auth::user()->isAdmin() && !$order->isDraft()) {
            return back()->with('error', 'เฉพาะ Admin หรือใบสั่งขายสถานะแบบร่างเท่านั้นที่สามารถลบได้');
        }

        $orderNo = $order->order_no;
        $order->lines()->delete();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'ลบใบสั่งขายเลขที่ ' . $orderNo . ' เรียบร้อยแล้ว');
    }

    public function print(SalesOrder $order)
    {
        $order->load(['customer', 'creator', 'lines.item']);
        return view('orders.print', compact('order'));
    }
}

