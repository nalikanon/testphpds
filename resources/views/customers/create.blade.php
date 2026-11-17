@extends('layouts.app')

@section('title', 'เพิ่มลูกค้าใหม่')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-person-plus text-primary me-2"></i> เพิ่มลูกค้าใหม่ (New Customer)</h5>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">รหัสลูกค้า (Customer Code) <span class="text-danger">*</span></label>
                        <input type="text" name="customer_code" class="form-control @error('customer_code') is-invalid @enderror" value="{{ old('customer_code') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อลูกค้า / บริษัท (Customer Name) <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="ระบุชื่อลูกค้า" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('customers.index') }}" class="btn btn-light">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> บันทึกข้อมูลลูกค้า
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

