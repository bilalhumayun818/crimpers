@extends('layouts.app')

@section('page-title', 'Sales History Detail')
@section('page-sub', 'Professional invoice detail view.')

@section('content')
    @php
        $customerName = $invoice->customer->name ?? $invoice->customer_name ?? 'Walk-in Customer';
        $itemCount = $invoice->items->count();
        $totalQty = $invoice->items->sum('quantity');
        $fbrStatus = $invoice->fbrLog->status ?? null;
    @endphp
    <style>
        .invd-wrap {
            font-family: 'Outfit', sans-serif;
            display: flex;
            flex-direction: column;
            gap: 18px;
            background: linear-gradient(145deg, #f8fafc 0%, #eef2f8 55%, #eaf0f7 100%);
            padding: 16px;
            border-radius: 22px;
            border: 1px solid #e5eaf2
        }

        .invd-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap
        }

        .invd-title {
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
            margin: 0;
            letter-spacing: -.02em
        }

        .invd-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 5px
        }

        .invd-actions {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .invd-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 38px;
            padding: 0 14px;
            border-radius: 11px;
            border: 1px solid #dbe3ee;
            background: #fff;
            color: #475569;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(15, 23, 42, .04)
        }

        .invd-btn:hover {
            border-color: #cbd5e1;
            color: #111827
        }

        .invd-btn-gold {
            background: linear-gradient(180deg, #fde68a 0%, #f7d96a 100%);
            border-color: #f1d76b;
            color: #111827;
            box-shadow: 0 8px 16px rgba(217, 119, 6, .22)
        }

        .invd-btn-gold:hover {
            background: linear-gradient(180deg, #fcd34d 0%, #fbbf24 100%)
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px
        }

        .hero-card {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            border: 1px solid #dfe7f2;
            border-radius: 14px;
            padding: 12px;
            box-shadow: 0 6px 14px rgba(15, 23, 42, .06)
        }

        .hero-label {
            font-size: 10px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 3px
        }

        .hero-val {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a
        }

        .hero-muted {
            font-size: 12px;
            font-weight: 700;
            color: #334155
        }

        .invd-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px
        }

        .card {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            border: 1px solid #dfe7f2;
            border-radius: 18px;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .06);
            overflow: hidden
        }

        .card-head {
            padding: 14px 16px;
            border-bottom: 1px solid #edf2f7;
            background: linear-gradient(180deg, #fafcff 0%, #f4f8fd 100%)
        }

        .card-title {
            font-size: 12px;
            font-weight: 900;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: .08em
        }

        .card-body {
            padding: 14px 16px
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px
        }

        .meta-box {
            background: #f8fbff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px
        }

        .meta-label {
            font-size: 10px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 4px
        }

        .meta-val {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a
        }

        .table-wrap {
            overflow-x: auto
        }

        .items-table {
            width: 100%;
            border-collapse: collapse
        }

        .items-table th {
            font-size: 10px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .07em;
            text-align: left;
            padding: 11px;
            background: #f8fbff;
            border-bottom: 1px solid #edf2f7
        }

        .items-table td {
            padding: 11px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155
        }

        .items-table tbody tr:hover {
            background: #fffcf0
        }

        .items-table tr:last-child td {
            border-bottom: none
        }

        .amount {
            text-align: right;
            font-variant-numeric: tabular-nums
        }

        .item-name {
            font-weight: 700;
            color: #0f172a
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase
        }

        .badge-paid {
            background: #dcfce7;
            color: #166534
        }

        .badge-fbr-ok {
            background: #dcfce7;
            color: #166534
        }

        .badge-fbr-pending {
            background: #fef9c3;
            color: #92400e
        }

        .badge-fbr-fail {
            background: #fee2e2;
            color: #991b1b
        }

        .sum-list {
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .sum-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #475569
        }

        .sum-row b {
            color: #0f172a
        }

        .sum-total {
            margin-top: 6px;
            padding: 11px 12px;
            border: 1px solid #fde68a;
            background: #fffbeb;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            display: flex;
            justify-content: space-between
        }

        @media (max-width: 1024px) {

            .invd-grid,
            .hero-stats {
                grid-template-columns: 1fr
            }
        }
    </style>

    <div class="invd-wrap">
        <div class="invd-head">
            <div>
                <h1 class="invd-title">Invoice #{{ $invoice->invoice_no }}</h1>
                <div class="invd-sub">Created {{ $invoice->created_at->format('d M Y, h:i A') }} by
                    {{ $invoice->user->name ?? 'System' }}</div>
            </div>
            <div class="invd-actions">
                {{-- USER DEMAND: "no ticket logic" - Removing print receipt trigger --}}
                <a href="{{ route('invoices.index') }}" class="invd-btn btn-back">
                    <i data-lucide="chevron-left"></i> Back to History
                </a>
            </div>
        </div>
        <div class="hero-stats">
            <div class="hero-card">
                <div class="hero-label">Customer</div>
                <div class="hero-muted">{{ $customerName }}</div>
            </div>
            <div class="hero-card">
                <div class="hero-label">Items / Qty</div>
                <div class="hero-muted">{{ $itemCount }} / {{ $totalQty }}</div>
            </div>
            <div class="hero-card">
                <div class="hero-label">Payment Method</div>
                <div class="hero-muted">{{ ucfirst($invoice->payment_method ?? 'cash') }}</div>
            </div>
            <div class="hero-card">
                <div class="hero-label">Payable</div>
                <div class="hero-val">PKR {{ number_format((float) $invoice->payable_amount, 2) }}</div>
            </div>
        </div>

        <div class="invd-grid">
            <div style="display:flex;flex-direction:column;gap:16px">
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Invoice Info</div>
                    </div>
                    <div class="card-body">
                        <div class="meta-grid">
                            <div class="meta-box">
                                <div class="meta-label">Customer</div>
                                <div class="meta-val">{{ $customerName }}</div>
                            </div>
                            <div class="meta-box">
                                <div class="meta-label">Payment Method</div>
                                <div class="meta-val">{{ ucfirst($invoice->payment_method ?? 'cash') }}</div>
                            </div>
                            <div class="meta-box">
                                <div class="meta-label">Status</div>
                                <div class="meta-val"><span
                                        class="badge badge-paid">{{ strtoupper($invoice->status ?? 'paid') }}</span></div>
                            </div>
                            <div class="meta-box">
                                <div class="meta-label">Items / Qty</div>
                                <div class="meta-val">{{ $itemCount }} items / {{ $totalQty }} qty</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Items</div>
                    </div>
                    <div class="card-body table-wrap">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width:48px">#</th>
                                    <th>Item</th>
                                    <th style="width:120px">Type</th>
                                    <th class="amount" style="width:90px">Qty</th>
                                    <th class="amount" style="width:120px">Price</th>
                                    <th class="amount" style="width:140px">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="item-name">{{ $item->itemizable->name ?? 'Item' }}</td>
                                        <td>{{ class_basename($item->itemizable_type) }}</td>
                                        <td class="amount">{{ number_format($item->quantity, 2) }}</td>
                                        <td class="amount">PKR {{ number_format($item->price, 2) }}</td>
                                        <td class="amount">PKR {{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px">
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Totals</div>
                    </div>
                    <div class="card-body">
                        <div class="sum-list">
                            <div class="sum-row"><span>Subtotal</span><b>PKR
                                    {{ number_format((float) $invoice->total_amount, 2) }}</b></div>
                            <div class="sum-row"><span>Tax</span><b>PKR {{ number_format((float) $invoice->tax, 2) }}</b>
                            </div>
                            <div class="sum-row"><span>Discount</span><b>- PKR
                                    {{ number_format((float) $invoice->discount, 2) }}</b></div>
                            <div class="sum-total"><span>Payable</span><span>PKR
                                    {{ number_format((float) $invoice->payable_amount, 2) }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title">FBR Status</div>
                    </div>
                    <div class="card-body">
                        @if($fbrStatus === 'success')
                            <span class="badge badge-fbr-ok">Synced Successfully</span>
                        @elseif($fbrStatus === 'failed')
                            <span class="badge badge-fbr-fail">Sync Failed</span>
                        @elseif($fbrStatus)
                            <span class="badge badge-fbr-pending">{{ strtoupper($fbrStatus) }}</span>
                        @else
                            <span class="badge badge-fbr-pending">Not Synced</span>
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-head">
                        <div class="card-title">Quick Notes</div>
                    </div>
                    <div class="card-body" style="font-size:12px;color:#64748b;line-height:1.6">
                        Invoice detail is optimized for review. Use <b style="color:#0f172a">Print Receipt</b> for thermal
                        output and this page for complete transaction breakdown.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection