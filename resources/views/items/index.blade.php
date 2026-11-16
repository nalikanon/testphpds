@extends('layouts.app')

@section('title', 'Items - Sales Order System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">ข้อมูลสินค้า (Items)</h3>
        <p class="text-muted small mb-0">Master Data &bull; รายการสินค้าและราคาต่อหน่วย</p>
    </div>
    <a href="{{ route('items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-1"></i> เพิ่มสินค้าใหม่
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('items.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยรหัส ชื่อ หรือรายละเอียดสินค้า..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">ค้นหา</button>
            </div>
            @if($search)
            <div class="col-md-2">
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary w-100">ล้างการค้นหา</a>
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
                        <th class="ps-3" style="width: 130px;">รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>รายละเอียด</th>
                        <th class="text-center" style="width: 100px;">หน่วยนับ</th>
                        <th class="text-end" style="width: 150px;">ราคาต่อหน่วย (฿)</th>
                        <th class="text-center" style="width: 130px;">ประวัติการขาย</th>
                        <th class="text-end pe-3" style="width: 140px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="ps-3 fw-bold text-primary">{{ $item->item_code }}</td>
                        <td class="fw-semibold">{{ $item->item_name }}</td>
                        <td class="text-muted small">{{ Str::limit($item->description, 50) ?? '-' }}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->unit ?? '-' }}</span></td>
                        <td class="text-end fw-bold">฿{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $item->sales_lines_count }} ครั้ง</span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการลบสินค้านี้ใช่หรือไม่?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="ลบ" {{ $item->sales_lines_count > 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            ไม่พบข้อมูลสินค้า
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection

