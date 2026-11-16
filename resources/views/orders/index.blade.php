@extends('layouts.app')

@section('title', 'Sales Order Management (User + Admin)')

@section('content')
<!-- Header & New Order Button -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="bi bi-receipt-cutoff me-2 text-primary"></i>Sales Order Management (User + Admin)
        </h3>
        <p class="text-muted small mb-0">
            <span class="badge bg-secondary me-2">Permission</span>
            @if(Auth::user()->isAdmin())
                <span class="text-danger fw-semibold"><i class="bi bi-shield-lock me-1"></i>Admin: แสดงข้อมูลใบสั่งซื้อทั้งหมดในระบบ</span>
            @else
                <span class="text-primary fw-semibold"><i class="bi bi-person me-1"></i>User ({{ Auth::user()->username }}): แสดงเฉพาะใบสั่งซื้อของตนเองเท่านั้น</span>
            @endif
        </p>
    </div>
    <div class="mt-2 mt-sm-0">
        <!-- New Order Button -->
        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> New Order (สร้างใบสั่งซื้อใหม่)
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('orders.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">ค้นหา (Order No / Customer)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาเลขที่หรือชื่อลูกค้า..." value="{{ $search }}">
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">สถานะ (Status)</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- ทุกสถานะ --</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft (แบบร่าง)</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed (ยืนยันแล้ว)</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled (ยกเลิก)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">ลูกค้า (Customer from Master)</label>
                <select name="customer_id" class="form-select form-select-sm">
                    <option value="">-- เลือกลูกค้าทั้งหมด --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->customer_id }}" {{ $customerId == $c->customer_id ? 'selected' : '' }}>
                        {{ $c->customer_code }} - {{ $c->customer_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">ตั้งแต่วันที่</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">
                    <i class="bi bi-funnel me-1"></i> กรองข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rule Notes Banner -->
<div class="alert alert-info py-2 px-3 small d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <i class="bi bi-info-circle-fill me-1"></i>
        <strong>เงื่อนไขการทำงาน:</strong> 
        ถ้า <span class="badge bg-success">Confirmed</span> หรือ <span class="badge bg-danger">Cancelled</span> แล้ว จะไม่สามารถแก้ไข และลบได้
    </div>
    <div class="text-danger fw-semibold">
        <i class="bi bi-trash me-1"></i>Delete : status = draft เท่านั้น
    </div>
</div>

<!-- Data Grid (Table) -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light text-center small">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 130px;">Order No</th>
                        <th>Customer (ดึงมาจากMaster)</th>
                        <th style="width: 120px;">Date (วันที่สร้างรายการ)</th>
                        <th style="width: 140px;" class="text-end">
                            <div>Total</div>
                            <div class="text-muted fw-normal" style="font-size: 11px;">(คำนวณราคาสินค้าทั้งหมด)</div>
                        </th>
                        <th style="width: 130px;">
                            <div>Status</div>
                            <div class="text-muted fw-normal" style="font-size: 10px;">(Confirmed/Cancel แก้ไข/ลบไม่ได้)</div>
                        </th>
                        <th style="width: 110px;">CreatedBy</th>
                        <th style="width: 190px;" class="text-center">
                            <div>Actions</div>
                            <div class="text-danger fw-normal" style="font-size: 10px;">Delete: status = draft</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $index => $order)
                    <tr>
                        <!-- No -->
                        <td class="text-center fw-semibold text-muted">
                            {{ $orders->firstItem() + $index }}
                        </td>

                        <!-- Order No (คลิกดูรายละเอียด) -->
                        <td class="fw-bold text-center">
                            <a href="{{ route('orders.show', $order) }}" class="text-decoration-underline text-primary" title="คลิกเพื่อดูรายละเอียด">
                                {{ $order->order_no }}
                            </a>
                        </td>

                        <!-- Customer (Master) -->
                        <td>
                            <div class="fw-semibold text-dark">{{ $order->customer->customer_name ?? '-' }}</div>
                            <small class="text-muted">รหัส: {{ $order->customer->customer_code ?? '' }}</small>
                        </td>

                        <!-- Date -->
                        <td class="text-center text-nowrap">
                            {{ $order->order_date ? $order->order_date->format('Y-m-d') : '-' }}
                        </td>

                        <!-- Total -->
                        <td class="text-end fw-bold fs-6">
                            ฿{{ number_format($order->grand_total, 2) }}
                        </td>

                        <!-- Status -->
                        <td class="text-center">
                            @if($order->status === 'confirmed')
                                <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i>Confirmed</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Cancelled</span>
                            @else
                                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-file-earmark-text me-1"></i>Draft</span>
                            @endif
                        </td>

                        <!-- CreatedBy -->
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-person me-1"></i>{{ $order->creator->username ?? '-' }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- View (ดูรายละเอียด) -->
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-info" title="View (ดูรายละเอียด)">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Edit (แก้ไข - เฉพาะ Draft) -->
                                @if($order->isDraft())
                                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-warning text-dark" title="Edit (แก้ไข - เฉพาะ Draft)">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                @else
                                    <button type="button" class="btn btn-outline-secondary opacity-50" disabled title="Confirmed / Cancelled แล้ว ไม่สามารถแก้ไขได้">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                @endif

                                <!-- Delete (ลบ - เฉพาะ Draft) -->
                                @if($order->isDraft())
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('ยืนยันลบใบสั่งขายเลขที่ {{ $order->order_no }} (สถานะ Draft) ใช่หรือไม่?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete (ลบ - เฉพาะ status = draft)">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-outline-secondary opacity-50" disabled title="Confirmed / Cancelled แล้ว ไม่สามารถลบได้">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif

                                <!-- Print PDF -->
                                <a href="{{ route('orders.print', $order) }}" target="_blank" class="btn btn-outline-secondary" title="Print PDF">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            ไม่พบรายการใบสั่งซื้อ
                            <div class="mt-2">
                                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> คลิกที่นี่เพื่อเปิดใบสั่งซื้อใหม่
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            แสดงรายการที่ {{ $orders->firstItem() }} ถึง {{ $orders->lastItem() }} จากทั้งหมด {{ $orders->total() }} รายการ
        </div>
        <div>
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>
@endsection
