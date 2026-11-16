@extends('layouts.app')

@section('title', 'ข้อมูลส่วนตัวและเปลี่ยนรหัสผ่าน')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i> ข้อมูลผู้ใช้งานและเปลี่ยนรหัสผ่าน</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4 p-3 bg-light rounded border">
                    <div class="row">
                        <div class="col-sm-4 text-muted">ชื่อผู้ใช้งาน:</div>
                        <div class="col-sm-8 fw-bold">{{ $user->username }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4 text-muted">บทบาท (Role):</div>
                        <div class="col-sm-8">
                            <span class="badge {{ $user->isAdmin() ? 'bg-danger' : 'bg-primary' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-4 text-muted">สร้างเมื่อ:</div>
                        <div class="col-sm-8 text-secondary">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3"><i class="bi bi-key-fill text-warning me-1"></i> เปลี่ยนรหัสผ่าน</h6>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านปัจจุบัน <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" minlength="6" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password" name="new_password_confirmation" class="form-control" minlength="6" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> บันทึกรหัสผ่านใหม่
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

