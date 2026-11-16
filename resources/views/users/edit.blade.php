@extends('layouts.app')

@section('title', 'แก้ไขผู้ใช้งาน ' . $user->username)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> แก้ไขผู้ใช้งาน: {{ $user->username }}</h5>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อผู้ใช้งาน (Username) <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">รหัสผ่านใหม่ (New Password)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="6" placeholder="ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน">
                        <div class="form-text">ระบุเฉพาะเมื่อต้องการรีเซ็ตรหัสผ่านใหม่</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">บทบาท / สิทธิ์ (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User (ผู้ใช้งานทั่วไป)</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบ)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-light">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

