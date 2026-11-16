@extends('layouts.app')

@section('title', 'แก้ไขสินค้า - Item Master')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> แก้ไขข้อมูลสินค้า (Item Master)</h5>
                <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('items.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">รหัสสินค้า (Item Code) <span class="text-danger">*</span></label>
                            <input type="text" name="item_code" class="form-control @error('item_code') is-invalid @enderror" value="{{ old('item_code', $item->item_code) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">หมวดหมู่สินค้า (Category)</label>
                            <input type="text" name="category" list="categoryList" class="form-control" value="{{ old('category', $item->category) }}" placeholder="ระบุหมวดหมู่">
                            <datalist id="categoryList">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อสินค้า (Item Name) <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror" value="{{ old('item_name', $item->item_name) }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">หน่วยนับ (Unit)</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', $item->unit) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ราคาต่อหน่วย (Unit Price) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">฿</span>
                                <input type="number" step="0.01" min="0" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $item->unit_price) }}" required>
                            </div>
                            <div class="form-text">Validation: price >= 0</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">รายละเอียดสินค้า (Description)</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('items.index') }}" class="btn btn-light">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> ปรับปรุงข้อมูลสินค้า
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
