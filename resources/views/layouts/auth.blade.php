<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order System - เข้าสู่ระบบ</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            padding: 20px 0;
        }
        .outer-window {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border: 2px solid #343a40;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .outer-header {
            background: #f8f9fa;
            border-bottom: 2px solid #343a40;
            padding: 14px 20px;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: #212529;
            letter-spacing: 0.5px;
        }
        .outer-body {
            padding: 35px 25px;
            background: #f4f6f9;
        }
        .login-box {
            background: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 30px 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .login-title {
            text-align: center;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 2px;
            margin-bottom: 24px;
            color: #212529;
        }
    </style>
</head>
<body>
    @yield('content')

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

