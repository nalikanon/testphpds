@extends('layouts.app')

@section('title', 'ใบสั่งขาย ' . $order->order_no)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold mb-0 text-dark">ใบสั่งขาย: {{ $order->order_no }}</h3>
            {!! $order->status_badge !!}
        </div>
        <p class="text-muted small mb-0 mt-1">
            สร้างเมื่อ {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }} โดย <strong>{{ $order->creator->username ?? '-' }}</strong>
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('orders.print', $order) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> พิมพ์เอกสาร
        </a>

        @if($order->isDraft())
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> แก้ไขใบสั่งขาย
            </a>

            <form action="{{ route('orders.confirm', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการยืนยันใบสั่งขายนี้ใช่หรือไม่? เมื่อยืนยันแล้วจะไม่สามารถแก้ไขรายการได้');">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> ยืนยันคำสั่งซื้อ (Confirm)
                </button>
            </form>
        @endif

        @if($order->isDraft())
            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการยกเลิกใบสั่งขายนี้ใช่หรือไม่?');">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-x-circle me-1"></i> ยกเลิก (Cancel)
                </button>
            </form>
        @endif

        <a href="{{ route('orders.index') }}" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left me-1"></i> รายการทั้งหมด
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Customer Details Card -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-building me-2"></i>ข้อมูลลูกค้า (Customer)</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;">รหัสลูกค้า:</td>
                        <td class="fw-bold">{{ $order->customer->customer_code ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ชื่อลูกค้า / บริษัท:</td>
                        <td class="fw-semibold fs-6">{{ $order->customer->customer_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">บันทึกในระบบ:</td>
                        <td>{{ $order->customer && $order->customer->created_at ? $order->customer->created_at->format('d/m/Y') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Summary Card -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>ข้อมูลคำสั่งซื้อ (Order Info)</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;">เลขที่เอกสาร:</td>
                        <td class="fw-bold text-primary">{{ $order->order_no }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">วันที่สั่งซื้อ:</td>
                        <td>{{ $order->order_date ? $order->order_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">กำหนดส่งสินค้า:</td>
                        <td>{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">หมายเหตุ:</td>
                        <td class="text-secondary">{{ $order->remark ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Order Lines Table -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-list-check me-2"></i>รายการสินค้า (Order Lines)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 130px;">รหัสสินค้า</th>
                        <th>ชื่อสินค้า / รายละเอียด</th>
                        <th style="width: 90px;">หน่วย</th>
                        <th class="text-end" style="width: 110px;">จำนวน</th>
                        <th class="text-end" style="width: 130px;">ราคา/หน่วย (฿)</th>
                        <th class="text-end" style="width: 140px;">รวมเงิน (฿)</th>
                        <th style="min-width: 140px;">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->lines as $line)
                    <tr>
                        <td class="text-center fw-bold">{{ $line->line_no }}</td>
                        <td class="fw-bold text-primary">{{ $line->item->item_code ?? '-' }}</td>
                        <td>
                            <div class="fw-semibold">{{ $line->item->item_name ?? '-' }}</div>
                            @if($line->item && $line->item->description)
                            <div class="text-muted small">{{ $line->item->description }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $line->item->unit ?? '-' }}</td>
                        <td class="text-end">{{ number_format($line->quantity, 3) }}</td>
                        <td class="text-end">฿{{ number_format($line->unit_price, 2) }}</td>
                        <td class="text-end fw-bold">฿{{ number_format($line->line_total, 2) }}</td>
                        <td class="text-muted small">{{ $line->remark ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">ไม่มีรายการสินค้า</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Financial Summary Box -->
<div class="row justify-content-end mb-4">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted fw-semibold">ยอดรวมสินค้า (Total Amount):</td>
                        <td class="text-end fw-bold fs-6">฿{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">
                            ส่วนลดการค้า (Order Discount {{ number_format($order->discount_percent, 2) }}%):
                        </td>
                        <td class="text-end text-danger fw-semibold">
                            -฿{{ number_format($order->discount_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">ยอดหลังหักส่วนลด (Net Amount):</td>
                        <td class="text-end fw-bold">฿{{ number_format($order->net_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">
                            ภาษีมูลค่าเพิ่ม (VAT {{ number_format($order->vat_percent, 2) }}%):
                        </td>
                        <td class="text-end text-muted">
                            +฿{{ number_format($order->vat_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-2"></td>
                    </tr>
                    <tr class="table-primary rounded">
                        <td class="fw-bold fs-5 text-primary py-2 ps-2">ยอดรวมสุทธิ (Grand Total):</td>
                        <td class="text-end fw-bold fs-4 text-primary pe-2">฿{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

