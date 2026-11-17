<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display customer list.
     * User: Read Only | Admin: Role Full
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::query()
            ->when($search, function ($query, $search) {
                $query->where('customer_code', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
            })
            ->withCount('salesOrders')
            ->orderBy('customer_code')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถเพิ่มข้อมูลลูกค้าได้ (User: Read Only)');
        }

        return view('customers.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถเพิ่มข้อมูลลูกค้าได้ (User: Read Only)');
        }

        $validated = $request->validate([
            'customer_code' => ['required', 'string', 'max:20', 'unique:Customers,customer_code'],
            'customer_name' => ['required', 'string', 'max:100'],
        ], [
            'customer_code.required' => 'กรุณาระบุรหัสลูกค้า',
            'customer_code.unique' => 'รหัสลูกค้านี้มีอยู่ในระบบแล้ว',
            'customer_name.required' => 'กรุณาระบุชื่อลูกค้า',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'เพิ่มข้อมูลลูกค้าเรียบร้อยแล้ว');
    }

    public function edit(Customer $customer)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถแก้ไขข้อมูลลูกค้าได้ (User: Read Only)');
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถแก้ไขข้อมูลลูกค้าได้ (User: Read Only)');
        }

        $validated = $request->validate([
            'customer_code' => ['required', 'string', 'max:20', 'unique:Customers,customer_code,' . $customer->customer_id . ',customer_id'],
            'customer_name' => ['required', 'string', 'max:100'],
        ], [
            'customer_code.required' => 'กรุณาระบุรหัสลูกค้า',
            'customer_code.unique' => 'รหัสลูกค้านี้มีอยู่ในระบบแล้ว',
            'customer_name.required' => 'กรุณาระบุชื่อลูกค้า',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'ปรับปรุงข้อมูลลูกค้าเรียบร้อยแล้ว');
    }

    public function destroy(Customer $customer)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'เฉพาะ Admin เท่านั้นที่สามารถลบข้อมูลลูกค้าได้ (User: Read Only)');
        }

        if ($customer->salesOrders()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบลูกค้ารายนี้ได้ เนื่องจากมีใบสั่งขายที่อ้างอิงอยู่');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'ลบข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
}
