@extends('layouts.app')

@section('title', 'แก้ไขใบสั่งขาย ' . $order->order_no)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="fw-bold mb-0 text-dark">แก้ไขใบสั่งขาย (Edit Sales Order)</h3>
        <p class="text-muted small mb-0">เลขที่: <strong>{{ $order->order_no }}</strong> | สถานะ: {!! $order->status_badge !!}</p>
    </div>
    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ
    </a>
</div>

<form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
    @csrf
    @method('PUT')

    <!-- Header Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-file-text me-2"></i>ข้อมูลทั่วไป (Order Header)</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">เลขที่ใบสั่งขาย (Order No.) <span class="text-danger">*</span></label>
                    <input type="text" name="order_no" class="form-control fw-bold bg-light" value="{{ old('order_no', $order->order_no) }}" required readonly>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold">ลูกค้า (Customer) <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                        <option value="">-- เลือกลูกค้า --</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->customer_id }}" {{ old('customer_id', $order->customer_id) == $c->customer_id ? 'selected' : '' }}>
                            {{ $c->customer_code }} - {{ $c->customer_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">วันที่สั่งซื้อ (Order Date) <span class="text-danger">*</span></label>
                    <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $order->order_date ? $order->order_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">กำหนดส่ง (Delivery Date)</label>
                    <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '') }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">หมายเหตุ (Remark)</label>
                    <input type="text" name="remark" class="form-control" value="{{ old('remark', $order->remark) }}" placeholder="ระบุหมายเหตุ">
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-primary"><i class="bi bi-cart3 me-2"></i>รายการสินค้า (Order Lines)</h5>
            <button type="button" class="btn btn-sm btn-success" id="btnAddRow">
                <i class="bi bi-plus-circle me-1"></i> เพิ่มรายการสินค้า
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" id="linesTable">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="min-width: 260px;">สินค้า (Item) <span class="text-danger">*</span></th>
                            <th style="width: 90px;">หน่วย</th>
                            <th style="width: 120px;">จำนวน <span class="text-danger">*</span></th>
                            <th style="width: 130px;">ราคา/หน่วย (฿)</th>
                            <th style="width: 100px;">ส่วนลด (%)</th>
                            <th style="width: 140px;">รวมเงิน (฿)</th>
                            <th style="min-width: 150px;">หมายเหตุ</th>
                            <th style="width: 60px;">ลบ</th>
                        </tr>
                    </thead>
                    <tbody id="linesContainer">
                        <!-- Populated via existing lines or JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="row justify-content-end mb-4">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted fw-semibold">ยอดรวมสินค้า (Total Amount):</td>
                            <td class="text-end fw-bold fs-6" id="dispTotalAmount">฿0.00</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold align-middle">ส่วนลดการค้า (Order Discount):</td>
                            <td class="text-end">
                                <div class="input-group input-group-sm ms-auto" style="width: 160px;">
                                    <input type="number" step="0.01" min="0" max="100" name="discount_percent" id="discountPercent" class="form-control text-end" value="{{ old('discount_percent', $order->discount_percent) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="text-danger small mt-1" id="dispDiscountAmount">-฿0.00</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">ยอดหลังหักส่วนลด (Net Amount):</td>
                            <td class="text-end fw-bold" id="dispNetAmount">฿0.00</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold align-middle">ภาษีมูลค่าเพิ่ม (VAT):</td>
                            <td class="text-end">
                                <div class="input-group input-group-sm ms-auto" style="width: 160px;">
                                    <input type="number" step="0.01" min="0" max="100" name="vat_percent" id="vatPercent" class="form-control text-end" value="{{ old('vat_percent', $order->vat_percent) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="text-muted small mt-1" id="dispVatAmount">+฿0.00</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr class="my-2"></td>
                        </tr>
                        <tr class="table-primary rounded">
                            <td class="fw-bold fs-5 text-primary py-2 ps-2">ยอดรวมสุทธิ (Grand Total):</td>
                            <td class="text-end fw-bold fs-4 text-primary pe-2" id="dispGrandTotal">฿0.00</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="{{ route('orders.show', $order) }}" class="btn btn-light px-4">ยกเลิก</a>
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-save me-1"></i> ปรับปรุงใบสั่งขาย
        </button>
    </div>
</form>

<script>
const itemsData = @json($items);
const existingLines = @json($order->lines);
let rowIndex = 0;

function addRowWithData(item_id = '', unit = '-', qty = '1.000', price = '0.00', disc = '0.00', remark = '') {
    const tr = document.createElement('tr');
    tr.id = `line-row-${rowIndex}`;
    tr.innerHTML = `
        <td class="text-center fw-bold line-no">${rowIndex + 1}</td>
        <td>
            <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm item-select" required onchange="handleItemChange(this, ${rowIndex})">
                <option value="">-- เลือกสินค้า (Item Code + Name + Price) --</option>
                ${itemsData.map(it => `<option value="${it.item_id}" data-price="${it.unit_price}" data-unit="${it.unit || ''}" ${it.item_id == item_id ? 'selected' : ''}>${it.item_code} - ${it.item_name} (฿${parseFloat(it.unit_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})})</option>`).join('')}
            </select>
        </td>
        <td class="text-center"><span class="item-unit small text-muted">${unit}</span></td>
        <td>
            <input type="number" step="0.001" min="0.001" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-end line-qty" value="${qty}" required oninput="calculateRow(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm text-end line-price" value="${price}" required oninput="calculateRow(${rowIndex})">
        </td>
        <td>
            <input type="number" step="0.01" min="0" max="100" name="items[${rowIndex}][discount_percent]" class="form-control form-control-sm text-end line-disc" value="${disc}" oninput="calculateRow(${rowIndex})">
        </td>
        <td class="text-end fw-bold line-total-disp">฿0.00</td>
        <td>
            <input type="text" name="items[${rowIndex}][remark]" class="form-control form-control-sm" value="${remark}" placeholder="หมายเหตุรายการ">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLineRow(${rowIndex})">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    document.getElementById('linesContainer').appendChild(tr);
    calculateRow(rowIndex);
    rowIndex++;
    updateLineNumbers();
}

function handleItemChange(selectElem, rIdx) {
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    const tr = document.getElementById(`line-row-${rIdx}`);
    if (!tr) return;

    if (selectedOption && selectedOption.value) {
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const unit = selectedOption.getAttribute('data-unit') || '-';

        tr.querySelector('.item-unit').textContent = unit;
        tr.querySelector('.line-price').value = price.toFixed(2);
    } else {
        tr.querySelector('.item-unit').textContent = '-';
        tr.querySelector('.line-price').value = '0.00';
    }
    calculateRow(rIdx);
}

function calculateRow(rIdx) {
    const tr = document.getElementById(`line-row-${rIdx}`);
    if (!tr) return;

    const qty = parseFloat(tr.querySelector('.line-qty').value) || 0;
    const price = parseFloat(tr.querySelector('.line-price').value) || 0;
    const discPct = parseFloat(tr.querySelector('.line-disc').value) || 0;

    const gross = qty * price;
    const discAmt = gross * (discPct / 100);
    const lineTotal = Math.max(0, gross - discAmt);

    tr.querySelector('.line-total-disp').textContent = '฿' + lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    calculateOrderSummary();
}

function calculateOrderSummary() {
    let subtotal = 0;
    document.querySelectorAll('#linesContainer tr').forEach(tr => {
        const qty = parseFloat(tr.querySelector('.line-qty').value) || 0;
        const price = parseFloat(tr.querySelector('.line-price').value) || 0;
        const discPct = parseFloat(tr.querySelector('.line-disc').value) || 0;

        const gross = qty * price;
        const discAmt = gross * (discPct / 100);
        subtotal += Math.max(0, gross - discAmt);
    });

    const orderDiscPct = parseFloat(document.getElementById('discountPercent').value) || 0;
    const orderDiscAmt = subtotal * (orderDiscPct / 100);
    const netAmount = Math.max(0, subtotal - orderDiscAmt);

    const vatPct = parseFloat(document.getElementById('vatPercent').value) || 0;
    const vatAmt = netAmount * (vatPct / 100);
    const grandTotal = netAmount + vatAmt;

    document.getElementById('dispTotalAmount').textContent = '฿' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('dispDiscountAmount').textContent = '-฿' + orderDiscAmt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('dispNetAmount').textContent = '฿' + netAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('dispVatAmount').textContent = '+฿' + vatAmt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('dispGrandTotal').textContent = '฿' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function removeLineRow(rIdx) {
    const tr = document.getElementById(`line-row-${rIdx}`);
    if (tr) {
        tr.remove();
        updateLineNumbers();
        calculateOrderSummary();
    }
}

function updateLineNumbers() {
    let num = 1;
    document.querySelectorAll('#linesContainer tr').forEach(tr => {
        const noCol = tr.querySelector('.line-no');
        if (noCol) noCol.textContent = num++;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btnAddRow').addEventListener('click', () => addRowWithData());
    document.getElementById('discountPercent').addEventListener('input', calculateOrderSummary);
    document.getElementById('vatPercent').addEventListener('input', calculateOrderSummary);

    if (existingLines && existingLines.length > 0) {
        existingLines.forEach(l => {
            const itemObj = itemsData.find(it => it.item_id === l.item_id);
            addRowWithData(l.item_id, itemObj ? itemObj.unit : '-', l.quantity, l.unit_price, l.discount_percent, l.remark || '');
        });
    } else {
        addRowWithData();
    }
});
</script>
@endsection

