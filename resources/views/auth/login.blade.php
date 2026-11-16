@extends('layouts.auth')

@section('content')
<div class="outer-window">
    <!-- Window Header: Sales Order System -->
    <div class="outer-header">
        <i class="bi bi-box-seam me-2 text-primary"></i>Sales Order System
    </div>

    <!-- Window Body -->
    <div class="outer-body">
        <div class="login-box">
            <!-- Title: LOGIN -->
            <div class="login-title">LOGIN</div>

            <!-- Error Alerts Handling -->
            @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger py-2 px-3 small mb-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle-fill me-2 flex-shrink-0"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success py-2 px-3 small d-flex align-items-center mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                <div>{{ session('success') }}</div>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <!-- Username Input -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold text-secondary small mb-1">Username</label>
                    <input type="text" 
                           class="form-control @error('username') is-invalid @enderror" 
                           id="username" 
                           name="username" 
                           value="{{ old('username') }}" 
                           placeholder="Enter your username" 
                           required 
                           autofocus>
                </div>

                <!-- Password Input -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold text-secondary small mb-1">Password:</label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password" 
                           required>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label small text-muted" for="remember">Remember Me</label>
                </div>

                <!-- Login Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary py-2 fw-bold tracking-wider">
                        LOGIN
                    </button>
                </div>

                <!-- Forgot Password -->
                <div class="text-center">
                    <a href="#" class="text-decoration-none small text-muted" data-bs-toggle="modal" data-bs-target="#forgotModal">
                        Forgot Password?
                    </a>
                </div>
            </form>
        </div>

        <!-- Quick Demo Credentials helper buttons -->
        <div class="mt-3 p-2 bg-white rounded border text-center small text-muted">
            <div class="fw-semibold mb-1"><i class="bi bi-shield-check me-1"></i>คลิกเพื่อใส่ข้อมูลทดสอบ (Demo Users):</div>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="fillForm('admin', 'admin123')">
                    <strong>Admin</strong> (admin / admin123)
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="fillForm('user', 'user123')">
                    <strong>User</strong> (user / user123)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-key-fill text-warning me-2"></i>Forgot Password?</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small">
                <p>หากคุณลืมรหัสผ่าน กรุณาติดต่อผู้ดูแลระบบ (System Administrator) เพื่อขอรีเซ็ตรหัสผ่านใหม่</p>
                <div class="alert alert-light border mb-0">
                    <strong>ข้อมูลผู้ดูแลระบบเริ่มต้น:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin123</code>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<script>
function fillForm(u, p) {
    document.getElementById('username').value = u;
    document.getElementById('password').value = p;
}
</script>
@endsection

