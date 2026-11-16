<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พิมพ์ใบสั่งขาย - {{ $order->order_no }}</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #212529;
        }
        .invoice-box {
            max-width: 850px;
            margin: 20px auto;
            padding: 30px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .invoice-box {
                border: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Print Actions Header (Hidden in Print) -->
    <div class="container my-3 no-print">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm border">
            <div>
                <strong>เอกสารใบสั่งขาย: {{ $order->order_no }}</strong>
                <span class="ms-2">{!! $order->status_badge !!}</span>
            </div>
            <div>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> สั่งพิมพ์เอกสาร
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="window.close()">
                    <i class="bi bi-x me-1"></i> ปิดหน้านี้
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Invoice Box -->
    <div class="invoice-box shadow-sm">
        <!-- Header -->
        <div class="row align-items-center border-bottom pb-4 mb-4">
            <div class="col-7">
                <h3 class="fw-bold text-primary mb-1">
                    <i class="bi bi-cart-check-fill me-2"></i>บริษัท เซลส์ ออเดอร์ จำกัด
                </h3>
                <div class="text-muted small">
                    123/45 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110<br>
                    โทรศัพท์: 02-123-4567 | อีเมล: contact@salesorder.co.th<br>
                    เลขประจำตัวผู้เสียภาษี: 0105559998887
                </div>
            </div>
            <div class="col-5 text-end">
                <h4 class="fw-bold text-dark text-uppercase mb-1">ใบสั่งขาย</h4>
                <div class="text-muted small text-uppercase mb-2">SALES ORDER</div>
                <div class="p-2 bg-light rounded text-start small border">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">เลขที่ใบสั่งขาย:</span>
                        <span class="fw-bold text-primary">{{ $order->order_no }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">วันที่สั่งซื้อ:</span>
                        <span>{{ $order->order_date ? $order->order_date->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">กำหนดส่งสินค้า:</span>
                        <span>{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">สถานะ:</span>
                        <span class="fw-semibold">{{ strtoupper($order->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="p-3 bg-light rounded border">
                    <div class="fw-bold text-secondary mb-1">ข้อมูลลูกค้า (Customer Details):</div>
                    <div class="fw-bold fs-6">{{ $order->customer->customer_name ?? '-' }}</div>
                    <div class="text-muted small">
                        รหัสลูกค้า: <strong>{{ $order->customer->customer_code ?? '-' }}</strong>
                    </div>
                    @if($order->remark)
                    <div class="mt-2 text-muted small">
                        <strong>หมายเหตุคำสั่งซื้อ:</strong> {{ $order->remark }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center small">
                    <tr>
                        <th style="width: 40px;">ลำดับ</th>
                        <th style="width: 120px;">รหัสสินค้า</th>
                        <th>รายการสินค้า</th>
                        <th style="width: 70px;">หน่วย</th>
                        <th class="text-end" style="width: 90px;">จำนวน</th>
                        <th class="text-end" style="width: 110px;">ราคา/หน่วย</th>
                        <th class="text-end" style="width: 90px;">ส่วนลด</th>
                        <th class="text-end" style="width: 130px;">รวมเงิน (฿)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->lines as $line)
                    <tr>
                        <td class="text-center">{{ $line->line_no }}</td>
                        <td class="fw-semibold text-primary">{{ $line->item->item_code ?? '-' }}</td>
                        <td>
                            <div class="fw-semibold">{{ $line->item->item_name ?? '-' }}</div>
                            @if($line->remark)
                            <small class="text-muted">({{ $line->remark }})</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $line->item->unit ?? '-' }}</td>
                        <td class="text-end">{{ number_format($line->quantity, 3) }}</td>
                        <td class="text-end">{{ number_format($line->unit_price, 2) }}</td>
                        <td class="text-end">
                            {{ $line->discount_percent > 0 ? number_format($line->discount_percent, 2).'%' : '-' }}
                        </td>
                        <td class="text-end fw-bold">{{ number_format($line->line_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Signatures -->
        <div class="row align-items-start mb-5">
            <div class="col-6">
                <div class="border rounded p-3 text-muted small">
                    <strong>เงื่อนไขและการชำระเงิน:</strong><br>
                    1. สินค้าตามใบสั่งขายนี้จะถูกจัดส่งตามวันที่ระบุ<br>
                    2. ชำระเงินภายใน 30 วันนับจากวันที่ได้รับสินค้า<br>
                    3. การยกเลิกต้องแจ้งล่วงหน้าอย่างน้อย 3 วันทำการ
                </div>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless text-end mb-0">
                    <tr>
                        <td class="text-muted">ยอดรวมสินค้า:</td>
                        <td class="fw-semibold" style="width: 140px;">฿{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ส่วนลดการค้า ({{ number_format($order->discount_percent, 2) }}%):</td>
                        <td class="text-danger">-฿{{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ยอดหลังหักส่วนลด:</td>
                        <td class="fw-bold">฿{{ number_format($order->net_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">ภาษีมูลค่าเพิ่ม VAT ({{ number_format($order->vat_percent, 2) }}%):</td>
                        <td>฿{{ number_format($order->vat_amount, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold fs-6 pt-2">ยอดรวมสุทธิ (Grand Total):</td>
                        <td class="fw-bold fs-5 text-primary pt-2">฿{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signatures Box -->
        <div class="row text-center pt-4 border-top">
            <div class="col-4">
                <div style="border-bottom: 1px dotted #999; height: 50px; margin: 0 20px;"></div>
                <div class="small fw-semibold mt-2">ผู้จัดทำ (Created By)</div>
                <div class="small text-muted">{{ $order->creator->username ?? '-' }}</div>
            </div>
            <div class="col-4">
                <div style="border-bottom: 1px dotted #999; height: 50px; margin: 0 20px;"></div>
                <div class="small fw-semibold mt-2">ผู้อนุมัติ (Authorized By)</div>
                <div class="small text-muted">วันที่: ____/____/________</div>
            </div>
            <div class="col-4">
                <div style="border-bottom: 1px dotted #999; height: 50px; margin: 0 20px;"></div>
                <div class="small fw-semibold mt-2">ผู้รับสินค้า (Received By)</div>
                <div class="small text-muted">วันที่: ____/____/________</div>
            </div>
        </div>
    </div>
</body>
</html>

