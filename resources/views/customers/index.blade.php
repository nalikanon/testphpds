@extends('layouts.app')

@section('title', 'Customers - Sales Order System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">ข้อมูลลูกค้า (Customers)</h3>
        <p class="text-muted small mb-0">Master Data &bull; จัดการรายชื่อลูกค้าและบริษัทคู่ค้า</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-1"></i> เพิ่มลูกค้าใหม่
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('customers.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยรหัสหรือชื่อลูกค้า..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">ค้นหา</button>
            </div>
            @if($search)
            <div class="col-md-2">
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary w-100">ล้างการค้นหา</a>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 140px;">รหัสลูกค้า</th>
                        <th>ชื่อลูกค้า / บริษัท</th>
                        <th class="text-center" style="width: 160px;">ใบสั่งขายทั้งหมด</th>
                        <th class="text-center" style="width: 160px;">วันที่บันทึก</th>
                        <th class="text-end pe-3" style="width: 160px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                    <tr>
                        <td class="ps-3 fw-bold text-primary">{{ $c->customer_code }}</td>
                        <td class="fw-semibold">{{ $c->customer_name }}</td>
                        <td class="text-center">
                            @if($c->sales_orders_count > 0)
                                <a href="{{ route('orders.index', ['customer_id' => $c->customer_id]) }}" class="badge bg-info text-dark text-decoration-none">
                                    {{ $c->sales_orders_count }} รายการ
                                </a>
                            @else
                                <span class="badge bg-light text-muted">0 รายการ</span>
                            @endif
                        </td>
                        <td class="text-center text-muted small">
                            {{ $c->created_at ? $c->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customers.edit', $c) }}" class="btn btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการลบลูกค้ารายนี้ใช่หรือไม่?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="ลบ" {{ $c->sales_orders_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            ไม่พบข้อมูลลูกค้า
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection

