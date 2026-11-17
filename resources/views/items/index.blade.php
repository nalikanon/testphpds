@extends('layouts.app')

@section('title', 'Items Master - Sales Order System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">ข้อมูลสินค้า (Item Master)</h3>
        <p class="text-muted small mb-0">
            Master Data &bull; 
            @if(Auth::user()->isAdmin())
                <span class="badge bg-success">Item Master: Admin Role Full (เพิ่ม-แก้ไข-ลบ-จัดหมวดหมู่สินค้า)</span>
            @else
                <span class="badge bg-secondary">Items: User Read Only (ดูข้อมูลสินค้าเท่านั้น)</span>
            @endif
        </p>
    </div>
    @if(Auth::user()->isAdmin())
    <a href="{{ route('items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-1"></i> เพิ่มสินค้าใหม่ (Admin Only)
    </a>
    @endif
</div>

<!-- Filter & Search by Code, Name, Category -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('items.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยรหัส ชื่อ หรือรายละเอียดสินค้า..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">-- ทุกหมวดหมู่สินค้า --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">ค้นหา</button>
            </div>
            @if($search || $category)
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
                        <th class="ps-3" style="width: 130px;">Item Code</th>
                        <th>Item Name</th>
                        <th style="width: 150px;">หมวดหมู่ (Category)</th>
                        <th class="text-center" style="width: 90px;">Unit</th>
                        <th class="text-end" style="width: 140px;">Unit Price (฿)</th>
                        <th class="text-center" style="width: 120px;">ประวัติการขาย</th>
                        <th class="text-end pe-3" style="width: 140px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="ps-3 fw-bold text-primary">{{ $item->item_code }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->item_name }}</div>
                            @if($item->description)
                            <div class="text-muted small">{{ Str::limit($item->description, 40) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $item->category ?? '-' }}</span>
                        </td>
                        <td class="text-center"><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $item->unit ?? '-' }}</span></td>
                        <td class="text-end fw-bold">฿{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $item->sales_lines_count }} ครั้ง</span>
                        </td>
                        <td class="text-end pe-3">
                            @if(Auth::user()->isAdmin())
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
                            @else
                                <span class="badge bg-light text-muted border">Read Only</span>
                            @endif
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
