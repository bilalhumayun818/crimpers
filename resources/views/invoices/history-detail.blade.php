@extends('layouts.app')

@section('page-title', 'Invoice Detail')
@section('page-sub', 'Transaction record and financial summary.')

@section('content')
@php
    $customerName = $invoice->customer->name ?? $invoice->customer_name ?? 'Walk-in Customer';
    $staffName    = $invoice->user->name ?? 'System';
    $fbrSync      = $invoice->fbrLog ? ucfirst($invoice->fbrLog->status) : 'Not Synced';
    $fbrColor     = $invoice->fbrLog && $invoice->fbrLog->status === 'success' ? '#16a34a' : '#f59e0b';
@endphp

<style>
.hd-wrap{max-width:900px;margin:0 auto;font-family:'Outfit',sans-serif;display:flex;flex-direction:column;gap:16px}

/* Header */
.hd-header{background:#fff;border-radius:18px;border:1px solid #e5e7eb;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.hd-inv-no{font-size:20px;font-weight:900;color:#111827}
.hd-inv-date{font-size:12px;color:#9ca3af;font-weight:400;margin-top:2px}
.hd-back{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;font-size:13px;font-weight:700;color:#374151;text-decoration:none;transition:all .2s}
.hd-back:hover{border-color:#111827;color:#111827}
.hd-back svg{width:14px;height:14px}

/* Stats row */
.hd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.hd-stat{background:#fff;border-radius:14px;border:1px solid #e5e7eb;padding:14px 16px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.hd-stat-label{font-size:9px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px}
.hd-stat-val{font-size:14px;font-weight:800;color:#111827;line-height:1.2}

/* Main grid */
.hd-grid{display:grid;grid-template-columns:1fr 280px;gap:14px}

/* Cards */
.hd-card{background:#fff;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.hd-card-head{padding:12px 18px;border-bottom:1px solid #f3f4f6;font-size:11px;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;background:#fafafa}

/* Items table */
.hd-table{width:100%;border-collapse:collapse}
.hd-table th{text-align:left;padding:10px 16px;font-size:10px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;border-bottom:1px solid #f3f4f6;background:#fafafa}
.hd-table th:last-child{text-align:right}
.hd-table td{padding:12px 16px;font-size:13px;border-bottom:1px solid #f9fafb;vertical-align:middle}
.hd-table tr:last-child td{border-bottom:none}
.hd-table tr:hover td{background:#fafafa}
.item-name{font-weight:700;color:#111827}
.item-type{font-size:10px;color:#9ca3af;font-weight:500;margin-top:1px}

/* Summary */
.hd-summary{padding:16px}
.hd-sum-row{display:flex;justify-content:space-between;font-size:12px;color:#6b7280;font-weight:500;margin-bottom:8px}
.hd-sum-row span:last-child{font-weight:700;color:#374151}
.hd-sum-row.disc span:last-child{color:#ef4444}
.hd-divider{height:1px;background:#e5e7eb;margin:10px 0}
.hd-total-row{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:14px}
.hd-total-label{font-size:13px;font-weight:800;color:#111827}
.hd-total-val{font-size:20px;font-weight:900;color:#111827}

/* Payment info */
.hd-pay-row{display:flex;justify-content:space-between;font-size:12px;color:#6b7280;font-weight:500;margin-bottom:6px}
.hd-pay-row span:last-child{font-weight:700;color:#111827;text-transform:capitalize}
.hd-paid-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#dcfce7;border-radius:20px;font-size:10px;font-weight:800;color:#16a34a;text-transform:uppercase;letter-spacing:.05em}

/* Dark info card */
.hd-dark-card{background:#111827;border-radius:18px;padding:18px;text-align:center}
.hd-dark-card svg{width:28px;height:28px;color:#F7DF79;margin-bottom:10px}
.hd-dark-title{font-size:12px;font-weight:800;color:#fff;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.hd-dark-sub{font-size:11px;color:rgba(255,255,255,.4);line-height:1.6}
</style>

<div class="hd-wrap">

    {{-- Header --}}
    <div class="hd-header">
        <div>
            <div class="hd-inv-no">#{{ $invoice->invoice_no }}</div>
            <div class="hd-inv-date">{{ $invoice->created_at->format('d M Y, h:i A') }}</div>
        </div>
        <a href="{{ route('invoices.index') }}" class="hd-back">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            Back
        </a>
    </div>

    {{-- Stats --}}
    <div class="hd-stats">
        <div class="hd-stat">
            <div class="hd-stat-label">Customer</div>
            <div class="hd-stat-val">{{ $customerName }}</div>
        </div>
        <div class="hd-stat">
            <div class="hd-stat-label">Staff</div>
            <div class="hd-stat-val">{{ $staffName }}</div>
        </div>
        <div class="hd-stat">
            <div class="hd-stat-label">Payment</div>
            <div class="hd-stat-val" style="text-transform:capitalize">{{ $invoice->payment_method ?? 'Cash' }}</div>
        </div>
        <div class="hd-stat">
            <div class="hd-stat-label">FBR Status</div>
            <div class="hd-stat-val" style="color:{{ $fbrColor }}">{{ $fbrSync }}</div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="hd-grid">

        {{-- Items Table --}}
        <div class="hd-card">
            <div class="hd-card-head">Order Items ({{ $invoice->items->count() }})</div>
            <table class="hd-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th style="text-align:center">Qty</th>
                        <th style="text-align:right">Unit Price</th>
                        <th style="text-align:right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $idx => $item)
                    <tr>
                        <td style="color:#9ca3af;font-weight:700;font-size:11px">{{ str_pad($idx+1,2,'0',STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="item-name">{{ $item->itemizable->name ?? 'Item' }}</div>
                            <div class="item-type">{{ class_basename($item->itemizable_type ?? '') }}</div>
                        </td>
                        <td style="text-align:center;font-weight:700">{{ $item->quantity }}</td>
                        <td style="text-align:right;color:#6b7280">PKR {{ number_format($item->unit_price ?? $item->price, 0) }}</td>
                        <td style="text-align:right;font-weight:800;color:#111827">PKR {{ number_format($item->subtotal, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Right sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px">

            {{-- Financial Summary --}}
            <div class="hd-card">
                <div class="hd-card-head">Financial Summary</div>
                <div class="hd-summary">
                    <div class="hd-sum-row"><span>Subtotal</span><span>PKR {{ number_format($invoice->total_amount, 0) }}</span></div>
                    <div class="hd-sum-row"><span>Tax (5%)</span><span>PKR {{ number_format($invoice->tax ?? 0, 0) }}</span></div>
                    @if(($invoice->discount ?? 0) > 0)
                    <div class="hd-sum-row disc"><span>Discount</span><span>-PKR {{ number_format($invoice->discount, 0) }}</span></div>
                    @endif
                    <div class="hd-divider"></div>
                    <div class="hd-total-row">
                        <span class="hd-total-label">Total Paid</span>
                        <span class="hd-total-val">PKR {{ number_format($invoice->payable_amount, 0) }}</span>
                    </div>
                    @php
                        $tendered = $invoice->tendered_amount ?? $invoice->payable_amount;
                        $change = max(0, $tendered - $invoice->payable_amount);
                    @endphp
                    <div class="hd-pay-row"><span>Amount Received</span><span>PKR {{ number_format($tendered, 0) }}</span></div>
                    <div class="hd-pay-row"><span>Change Given</span><span>PKR {{ number_format($change, 0) }}</span></div>
                    <div class="hd-pay-row"><span>Status</span><span><span class="hd-paid-badge">✓ Paid</span></span></div>
                </div>
            </div>

            {{-- Info card --}}
            <div class="hd-dark-card">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <div class="hd-dark-title">Verified Record</div>
                <div class="hd-dark-sub">This transaction is finalized and recorded in the system.</div>
            </div>

        </div>
    </div>

</div>
@endsection
