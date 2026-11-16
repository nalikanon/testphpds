@extends('layouts.app')

@section('title', 'รายการใบสั่งขาย - Simple Sales Order Management System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">ใบสั่งขาย (Sales Orders)</h3>
        <p class="text-muted small mb-0">รายการใบสั่งขายทั้งหมด จัดการสถานะ และออกเอกสาร</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> เปิดใบสั่งขายใหม่
    </a>
</div>

<!-- Filter Card -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('orders.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">ค้นหา (เลขที่ / ลูกค้า)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขที่หรือชื่อลูกค้า..." value="{{ $search }}">
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">สถานะ</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- ทุกสถานะ --</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>แบบร่าง (Draft)</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว (Confirmed)</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>ยกเลิก (Cancelled)</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">ลูกค้า</label>
                <select name="customer_id" class="form-select form-select-sm">
                    <option value="">-- เลือกลูกค้า --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->customer_id }}" {{ $customerId == $c->customer_id ? 'selected' : '' }}>
                        {{ $c->customer_code }} - {{ $c->customer_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">ตั้งแต่วันที่</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">ถึงวันที่</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-sm btn-secondary w-100">
                    <i class="bi bi-funnel"></i> กรอง
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card shadow-sm">
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
                    @forelse($orders as $order)
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
                                @if(Auth::user()->isAdmin() || $order->isDraft())
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการลบใบสั่งขายเลขที่ {{ $order->order_no }} ใช่หรือไม่?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="ลบ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            ไม่พบรายการใบสั่งขายตามเงื่อนไข
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection

