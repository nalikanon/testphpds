@extends('layouts.app')

@section('title', 'Database Live Inspector - MSSQL SalesDB')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-database-check me-2 text-primary"></i>Database Live Inspector</h3>
        <p class="text-muted small mb-0">ดูข้อมูลสดแบบเรียลไทม์จากฐานข้อมูล MSSQL ของระบบ</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i> รีเฟรชข้อมูล (Refresh)
        </button>
    </div>
</div>

<!-- Server Connection Info Banner -->
<div class="card shadow-sm mb-4 border-primary">
    <div class="card-body p-3 bg-light">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small">เซิร์ฟเวอร์ที่เชื่อมต่อ (Connected Server):</div>
                <div class="fw-bold fs-5 text-primary">
                    <i class="bi bi-hdd-network me-1"></i> {{ $serverInfo->server_name }}
                </div>
                <small class="text-secondary">(ใน SSMS ให้ต่อด้วยชื่อนี้)</small>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">ฐานข้อมูล (Database):</div>
                <div class="fw-bold fs-5 text-success">
                    <i class="bi bi-database me-1"></i> {{ $serverInfo->db_name }}
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">เวลาที่ตรวจสอบล่าสุด:</div>
                <div class="fw-bold fs-6 text-dark">
                    <i class="bi bi-clock me-1"></i> {{ date('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Tables -->
<ul class="nav nav-tabs mb-3" id="dbTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-bold" id="users-tab" data-bs-toggle="tab" data-bs-target="#users-pane" type="button">
            <i class="bi bi-people me-1"></i> dbo.Users ({{ $users->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers-pane" type="button">
            <i class="bi bi-building me-1"></i> dbo.Customers ({{ $customers->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="items-tab" data-bs-toggle="tab" data-bs-target="#items-pane" type="button">
            <i class="bi bi-box-seam me-1"></i> dbo.Items ({{ $items->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders-pane" type="button">
            <i class="bi bi-receipt me-1"></i> dbo.Sales_Orders ({{ $orders->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="lines-tab" data-bs-toggle="tab" data-bs-target="#lines-pane" type="button">
            <i class="bi bi-list-check me-1"></i> dbo.Sales_Lines ({{ $lines->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="dbTabsContent">
    <!-- 1. Users Tab -->
    <div class="tab-pane fade show active" id="users-pane">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-2 fw-bold text-primary">Table: dbo.Users</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 font-monospace small">
                        <thead class="table-light">
                            <tr>
                                <th>user_id</th>
                                <th>username</th>
                                <th>role</th>
                                <th>password (Bcrypt Hash)</th>
                                <th>created_at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                            <tr>
                                <td class="fw-bold">{{ $u->user_id }}</td>
                                <td class="fw-bold text-primary">{{ $u->username }}</td>
                                <td><span class="badge {{ $u->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">{{ $u->role }}</span></td>
                                <td class="text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $u->password }}</td>
                                <td>{{ $u->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Customers Tab -->
    <div class="tab-pane fade" id="customers-pane">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-2 fw-bold text-primary">Table: dbo.Customers</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 font-monospace small">
                        <thead class="table-light">
                            <tr>
                                <th>customer_id</th>
                                <th>customer_code</th>
                                <th>customer_name</th>
                                <th>created_at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $c)
                            <tr>
                                <td class="fw-bold">{{ $c->customer_id }}</td>
                                <td class="fw-bold text-primary">{{ $c->customer_code }}</td>
                                <td>{{ $c->customer_name }}</td>
                                <td>{{ $c->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Items Tab -->
    <div class="tab-pane fade" id="items-pane">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-2 fw-bold text-primary">Table: dbo.Items</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 font-monospace small">
                        <thead class="table-light">
                            <tr>
                                <th>item_id</th>
                                <th>item_code</th>
                                <th>item_name</th>
                                <th>category</th>
                                <th>unit</th>
                                <th>unit_price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $it)
                            <tr>
                                <td class="fw-bold">{{ $it->item_id }}</td>
                                <td class="fw-bold text-primary">{{ $it->item_code }}</td>
                                <td>{{ $it->item_name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $it->category }}</span></td>
                                <td>{{ $it->unit }}</td>
                                <td class="text-end fw-bold">฿{{ number_format($it->unit_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Sales Orders Tab -->
    <div class="tab-pane fade" id="orders-pane">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-2 fw-bold text-primary">Table: dbo.Sales_Orders</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 font-monospace small">
                        <thead class="table-light">
                            <tr>
                                <th>order_id</th>
                                <th>order_no</th>
                                <th>customer_id</th>
                                <th>order_date</th>
                                <th>grand_total</th>
                                <th>status</th>
                                <th>created_by</th>
                                <th>created_at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $ord)
                            <tr>
                                <td class="fw-bold">{{ $ord->order_id }}</td>
                                <td class="fw-bold text-primary">{{ $ord->order_no }}</td>
                                <td>{{ $ord->customer_id }} ({{ $ord->customer->customer_code ?? '' }})</td>
                                <td>{{ $ord->order_date ? $ord->order_date->format('Y-m-d') : '' }}</td>
                                <td class="text-end fw-bold">฿{{ number_format($ord->grand_total, 2) }}</td>
                                <td><span class="badge bg-secondary">{{ $ord->status }}</span></td>
                                <td>{{ $ord->created_by }} ({{ $ord->creator->username ?? '' }})</td>
                                <td>{{ $ord->created_at }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-3 text-muted">ยังไม่มีข้อมูล</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Sales Lines Tab -->
    <div class="tab-pane fade" id="lines-pane">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-2 fw-bold text-primary">Table: dbo.Sales_Lines</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 font-monospace small">
                        <thead class="table-light">
                            <tr>
                                <th>line_id</th>
                                <th>order_id</th>
                                <th>item_id</th>
                                <th>quantity</th>
                                <th>unit_price</th>
                                <th>discount_percent</th>
                                <th>line_total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lines as $ln)
                            <tr>
                                <td class="fw-bold">{{ $ln->line_id }}</td>
                                <td>{{ $ln->order_id }} ({{ $ln->order->order_no ?? '' }})</td>
                                <td>{{ $ln->item_id }} ({{ $ln->item->item_code ?? '' }})</td>
                                <td class="text-end">{{ $ln->quantity }}</td>
                                <td class="text-end">฿{{ number_format($ln->unit_price, 2) }}</td>
                                <td class="text-end">{{ $ln->discount_percent }}%</td>
                                <td class="text-end fw-bold">฿{{ number_format($ln->line_total, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">ยังไม่มีข้อมูล</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

