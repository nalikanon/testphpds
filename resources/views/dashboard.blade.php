@extends('layouts.app')

@section('title', 'Dashboard - Sales Order System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">ภาพรวมระบบ (Dashboard)</h3>
        <p class="text-muted small mb-0">ระบบบริหารจัดการใบสั่งขาย (Sales Order Management System)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> เปิดใบสั่งขายใหม่
        </a>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('customers.create') }}" class="btn btn-outline-secondary">
            <i class="bi bi-person-plus me-1"></i> เพิ่มลูกค้า (Admin Only)
        </a>
        @endif
    </div>
</div>

<!-- Metric Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">ยอดขายยืนยันแล้ว (THB)</div>
                        <h4 class="fw-bold text-primary mb-0 mt-1">฿{{ number_format($confirmedSalesAmount, 2) }}</h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">ใบสั่งขายทั้งหมด</div>
                        <h4 class="fw-bold text-info mb-0 mt-1">{{ number_format($totalOrders) }} รายการ</h4>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">แบบร่าง (Draft)</div>
                        <h4 class="fw-bold text-warning mb-0 mt-1">{{ number_format($draftOrders) }} รายการ</h4>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">ยืนยันแล้ว (Confirmed)</div>
                        <h4 class="fw-bold text-success mb-0 mt-1">{{ number_format($confirmedOrders) }} รายการ</h4>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Master Data Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-white p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-light p-3 rounded me-3 text-secondary">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ number_format($totalCustomers) }}</h5>
                    <div class="text-muted small">จำนวนลูกค้าทั้งหมด</div>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-primary">ดูข้อมูลลูกค้า</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-light p-3 rounded me-3 text-secondary">
                    <i class="bi bi-box-seam-fill fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ number_format($totalItems) }}</h5>
                    <div class="text-muted small">จำนวนสินค้าทั้งหมด</div>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-primary">ดูข้อมูลสินค้า</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="bg-light p-3 rounded me-3 text-danger">
                    <i class="bi bi-x-circle-fill fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ number_format($cancelledOrders) }}</h5>
                    <div class="text-muted small">ใบสั่งขายที่ยกเลิก</div>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="btn btn-sm btn-outline-secondary">ดูรายการ</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-clock-history me-2 text-primary"></i> ใบสั่งขายล่าสุด (Recent Orders)
        </h5>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">
            ดูใบสั่งขายทั้งหมด <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">เลขที่ใบสั่งขาย</th>
                        <th>ลูกค้า</th>
                        <th>วันที่สั่ง</th>
                        <th>กำหนดส่ง</th>
                        <th class="text-end">ยอดรวมสุทธิ (฿)</th>
                        <th class="text-center">สถานะ</th>
                        <th>ผู้บันทึก</th>
                        <th class="text-end pe-3">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="ps-3 fw-bold">
                            <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-primary">
                                {{ $order->order_no }}
                            </a>
                        </td>
                        <td>
                            <div>{{ $order->customer->customer_name ?? '-' }}</div>
                            <small class="text-muted">{{ $order->customer->customer_code ?? '' }}</small>
                        </td>
                        <td>{{ $order->order_date ? $order->order_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</td>
                        <td class="text-end fw-bold">฿{{ number_format($order->grand_total, 2) }}</td>
                        <td class="text-center">{!! $order->status_badge !!}</td>
                        <td><small class="text-muted">{{ $order->creator->username ?? '-' }}</small></td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary" title="ดูรายละเอียด">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('orders.print', $order) }}" target="_blank" class="btn btn-outline-secondary" title="พิมพ์">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @if($order->isDraft())
                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            ยังไม่มีข้อมูลใบสั่งขายในระบบ
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

