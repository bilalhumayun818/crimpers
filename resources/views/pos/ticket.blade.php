<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&nbsp;</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }

        .ticket-wrapper {
            width: 76mm;
            background: #fff;
            padding: 15px 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        .dashed-line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .header {
            margin-bottom: 10px;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 900;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .header-info {
            font-size: 11px;
            margin-bottom: 2px;
        }

        .meta-section {
            font-size: 11px;
            margin-bottom: 10px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .items-table th {
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            text-align: right;
        }

        .items-table th:first-child {
            text-align: left;
        }

        .items-table td {
            padding: 2px 0;
        }

        .item-desc-row {
            font-weight: bold;
        }

        .item-values-row td {
            text-align: right;
            padding-bottom: 2px;
        }

        .item-values-row td:first-child {
            text-align: left;
        }

        .summary-section {
            margin-top: 5px;
            font-size: 11px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
        }

        .summary-row.total-payable {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .payment-section {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 8px;
        }

        .footer-notes {
            font-size: 10px;
            margin-top: 10px;
            line-height: 1.4;
        }

        .fbr-section {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 15px;
        }

        .fbr-invoice-label {
            font-size: 16px;
            font-weight: bold;
        }

        .fbr-invoice-num {
            font-size: 18px;
            font-weight: 900;
            margin: 5px 0 15px 0;
            letter-spacing: 0.5px;
        }

        .fbr-footer-layout {
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin-top: 10px;
        }

        .fbr-logo {
            width: 90px;
        }

        .qr-code {
            width: 90px;
            height: 90px;
        }

        .loyalty-section {
            margin-top: 10px;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
                background: #fff;
            }

            .ticket-wrapper {
                box-shadow: none;
                margin: 0;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>
    <div class="ticket-wrapper">
        <!-- Header -->
        <div class="header text-center">
            <div class="brand-name">The Crimpers</div>
            <div class="header-info">Shop no. 5, Sargodha Rd, inside Pearl City Plaza, Canal Block Shadman Town,
                Faisalabad</div>
            <div class="header-info">Phone: 0300 7614788</div>
            <div class="header-info">PNTN: 5209042-4</div>
        </div>

        <!-- Metadata -->
        <div class="meta-section" style="font-size: 9px; white-space: nowrap;">
            <div class="meta-row">
                <span>Invoice #: <strong>{{ $invoice->invoice_no }}</strong></span>
            </div>
            <div class="meta-row" style="margin-bottom: 5px;">
                <span>Cashier: {{ strtoupper($invoice->user->name ?? 'ADMIN') }}</span>
                <span>{{ $invoice->created_at->format('d/m/Y h:i:s A') }}</span>
            </div>
            <div class="meta-row">
                <span>Mode of Payment: {{ strtoupper($invoice->payment_method) }}</span>
            </div>
            <div class="meta-row">
                <span>Customer:
                    {{ strtoupper($invoice->customer_name ?: ($invoice->customer->name ?? 'CASH SALES-WALKING CUSTOMER A/C')) }}</span>
            </div>
            @if($invoice->remarks)
                <div class="meta-row">
                    <span>Remarks: {{ $invoice->remarks }}</span>
                </div>
            @endif
        </div>

        <!-- Items Table -->
        <table class="items-table" style="font-size: 10px;">
            <thead>
                <tr>
                    <th style="width: 10px; text-align: left;">#</th>
                    <th style="text-align: left;">Description</th>
                    <th>Price</th>
                    <th>GST Rate</th>
                    <th>Qty</th>
                    <th>GST</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr class="item-desc-row">
                        <td>{{ $index + 1 }}</td>
                        <td colspan="6" style="text-transform: uppercase;">{{ $item->itemizable->name ?? 'Product Item' }}</td>
                    </tr>
                    <tr class="item-values-row">
                        <td colspan="2"></td>
                        <td>{{ number_format($item->price, 2) }}</td>
                        <td>{{ number_format($item->tax_rate ?? 5, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->tax, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="dashed-line"></div>

        <!-- Summary Section -->
        <div style="margin-top: 3px; font-size: 11px; line-height: 1.1;">
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 2px;">
                <span style="font-size: 10px;">Total Qty: {{ $invoice->items->sum('quantity') }}</span>
                <div style="display: flex; gap: 8px;">
                    <span class="font-bold" style="font-size: 12px;">Total Amount:</span>
                    <span style="min-width: 70px; text-align: right; font-size: 12px; font-weight: bold;">{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; align-items: baseline; margin-bottom: 2px;">
                <div style="display: flex; gap: 8px;">
                    <span>Discount:</span>
                    <span style="min-width: 70px; text-align: right;">{{ number_format($invoice->discount, 2) }}</span>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 2px;">
                <span style="font-size: 10px;">GST: {{ number_format($invoice->tax, 2) }}</span>
                <div style="display: flex; gap: 8px;">
                    <span>POS Service Fee:</span>
                    <span style="min-width: 70px; text-align: right;">{{ number_format($invoice->service_fee ?? 1.00, 2) }}</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end;">
                <div style="width: 150px; border-top: 1px solid #000; margin: 2px 0;"></div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; font-size: 14px; font-weight: bold; margin-bottom: 2px;">
                <div style="display: flex; gap: 8px;">
                    <span>Payable:</span>
                    <span style="min-width: 70px; text-align: right;">{{ number_format($invoice->payable_amount, 2) }}</span>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <div style="width: 160px; border-top: 1px dashed #000; margin: 4px 0;"></div>
            </div>

            <div style="display: flex; flex-direction: column; align-items: flex-end; font-weight: bold; font-size: 12px; line-height: 1.2;">
                <div style="display: flex; gap: 8px; margin-bottom: 1px;">
                    <span>Amount Received:</span>
                    <span style="min-width: 70px; text-align: right;">{{ number_format($invoice->tendered_amount ?? $invoice->payable_amount, 2) }}</span>
                </div>
                <div style="display: flex; gap: 8px; font-weight: normal; font-size: 11px; margin-bottom: 1px;">
                    <span>Amount Charged:</span>
                    <span style="min-width: 70px; text-align: right;">{{ number_format($invoice->payable_amount, 2) }}</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <span>Balance/Change:</span>
                    <span style="min-width: 70px; text-align: right;">{{ number_format(max(0, ($invoice->tendered_amount ?? $invoice->payable_amount) - $invoice->payable_amount), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="footer-notes text-center">
            <div class="footer-note" style="margin-top: 15px; text-align: left; font-size: 9px; line-height: 1.2;">
                <div>Thanks For Visiting</div>
                <div>For Complaint & Queries 0300 7614788</div>
                <div style="font-size: 8px; margin-top: 2px;">(Software developed by BROSHTech - no 0317 7676560)</div>
            </div>
        </div>

        <!-- FBR Section -->
        <div class="fbr-section text-center">
            <div style="margin-top: 20px; border-top: 1px solid #000; padding-top: 10px;">
                <div style="font-size: 11px;">FBR Invoice #:</div>
                <div style="font-size: 14px; font-weight: bold; margin-top: 3px;">
                    {{ $invoice->fbrLog->response['InvoiceNumber'] ?? '819568FDOO57331977*test*' }}
                </div>
            </div>

            <div class="fbr-footer-layout">
                <div class="text-left">
                    <img src="{{ asset('img/fbr-pos-logo.png') }}" alt="FBR POS Logo" class="fbr-logo"
                        style="filter: grayscale(1) invert(1) contrast(1.5);">
                    <div
                        style="font-size: 8px; font-weight: 900; text-align: center; margin-top: -2px; letter-spacing: 0.3px;">
                        INVOICING SYSTEM</div>
                </div>

                @php
                    $qrData = $invoice->fbrLog->response['InvoiceNumber'] ?? '135793260415135718887';
                @endphp
                <img class="qr-code"
                    src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrData) }}"
                    alt="FBR QR">
            </div>

            <p style="font-size: 9px; margin-top: 15px; font-weight: bold;">
                Verify this invoice through FBR TaxAsaan Mobile App or<br>
            </p>
        </div>

        <div class="no-print text-center" style="margin-top: 20px;">
            <button onclick="window.print()"
                style="background:#000; color:#fff; padding:10px 20px; border:none; cursor:pointer; font-weight:bold; margin-bottom: 5px; border-radius: 4px;">PRINT
                RECEIPT</button>
            <button onclick="window.close()"
                style="background:#ef4444; color:#fff; padding:10px 20px; border:none; cursor:pointer; font-weight:bold; border-radius: 4px;">CLOSE</button>
            <br><br>
            <a href="{{ route('pos.index') }}" style="color:#666; font-size:11px; text-decoration:none;">&larr; Back to
                POS</a>
        </div>
    </div>

    <script>
        window.onload = function () {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>