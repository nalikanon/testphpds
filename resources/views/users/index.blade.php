@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน - Simple Sales Order Management System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark">จัดการผู้ใช้งานในระบบ (User Management)</h3>
        <p class="text-muted small mb-0">เฉพาะผู้ดูแลระบบ (Admin) เท่านั้นที่สามารถจัดการผู้ใช้และสิทธิ์ได้</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-1"></i> เพิ่มผู้ใช้งานใหม่
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 80px;">#ID</th>
                        <th>ชื่อผู้ใช้งาน (Username)</th>
                        <th class="text-center" style="width: 140px;">สิทธิ์ (Role)</th>
                        <th class="text-center" style="width: 180px;">สร้างเมื่อ</th>
                        <th class="text-end pe-3" style="width: 150px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td class="ps-3 text-muted">{{ $u->user_id }}</td>
                        <td class="fw-bold text-dark">
                            <i class="bi bi-person-circle me-1 text-secondary"></i> {{ $u->username }}
                            @if($u->user_id === Auth::id())
                                <span class="badge bg-light text-primary border ms-1">คุณ (You)</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($u->isAdmin())
                                <span class="badge bg-danger"><i class="bi bi-shield-lock me-1"></i> ADMIN</span>
                            @else
                                <span class="badge bg-primary"><i class="bi bi-person me-1"></i> USER</span>
                            @endif
                        </td>
                        <td class="text-center text-muted small">
                            {{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.edit', $u) }}" class="btn btn-outline-primary" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($u->user_id !== Auth::id())
                                <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการลบผู้ใช้ {{ $u->username }} ใช่หรือไม่?');">
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection

