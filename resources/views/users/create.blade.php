@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้งานใหม่')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-person-plus text-primary me-2"></i> เพิ่มผู้ใช้งานใหม่ (Add User)</h5>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อผู้ใช้งาน (Username) <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="เช่น john, somchai" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">รหัสผ่าน (Password) <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="6" placeholder="รหัสผ่านอย่างน้อย 6 ตัวอักษร" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">บทบาท / สิทธิ์ (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User (ผู้ใช้งานทั่วไป - สร้าง/จัดการใบสั่งขาย)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบ - เข้าถึงได้ทุกฟังก์ชันและจัดการผู้ใช้)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-light">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> บันทึกผู้ใช้
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

