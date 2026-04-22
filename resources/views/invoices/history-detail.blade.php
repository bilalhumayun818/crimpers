@extends('layouts.app')

@section('page-title', 'Administrative Sales Record')
@section('page-sub', 'Detailed transaction analysis and bookkeeping record.')

@section('content')
{{-- 
    USER DEMAND: Professional Sales History Page (NO TICKET)
    This page is designed as a full-width administrative record. 
    It is NOT a thermal ticket and contains NO auto-print logic.
--}}
@php
    $customerName = $invoice->customer->name ?? $invoice->customer_name ?? 'Walk-in Customer';
    $staffName = $invoice->user->name ?? 'System Admin';
    $totalItems = $invoice->items->count();
    $fbrSync = $invoice->fbrLog ? strtoupper($invoice->fbrLog->status) : 'OFFLINE';
@endphp

<style>
/* ── High-End Administrative Aesthetics ── */
.history-container { max-width: 1300px; margin: 0 auto; display: flex; flex-direction: column; gap: 30px; font-family: 'Outfit', sans-serif; }
.history-header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 25px 35px; border-radius: 24px; border: 1px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
.history-id-block { display: flex; flex-direction: column; gap: 4px; }
.history-id-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; }
.history-id-val { font-size: 28px; font-weight: 900; color: #111827; letter-spacing: -0.02em; }

.history-stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.h-stat-card { background: #fff; padding: 22px; border-radius: 22px; border: 1px solid #e5e7eb; display: flex; flex-direction: column; gap: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
.h-stat-label { font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; }
.h-stat-val { font-size: 18px; font-weight: 800; color: #111827; }

.history-content-grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }
.history-main-card { background: #fff; border-radius: 28px; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
.h-card-head { padding: 20px 30px; border-bottom: 1px solid #f1f5f9; background: #fafafa; font-size: 13px; font-weight: 900; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; }
.h-card-body { padding: 30px; }

.h-table { width: 100%; border-collapse: collapse; }
.h-table th { text-align: left; padding: 15px; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; }
.h-table td { padding: 20px 15px; font-size: 14px; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
.h-table tr:last-child td { border-bottom: none; }
.item-desc { display: flex; flex-direction: column; gap: 2px; }
.item-primary { font-weight: 800; color: #111827; font-size: 15px; }
.item-secondary { font-size: 11px; color: #94a3b8; font-weight: 600; }

.h-summary-box { background: #f8fafc; border-radius: 24px; padding: 25px; display: flex; flex-direction: column; gap: 15px; border: 1px solid #eef2f6; }
.h-sum-row { display: flex; justify-content: space-between; font-size: 14px; color: #64748b; font-weight: 600; }
.h-sum-row b { color: #111827; }
.h-sum-total { border-top: 2px solid #e2e8f0; padding-top: 15px; margin-top: 5px; display: flex; justify-content: space-between; align-items: center; }
.h-total-label { font-size: 16px; font-weight: 900; color: #111827; text-transform: uppercase; }
.h-total-val { font-size: 24px; font-weight: 900; color: #F7DF79; text-shadow: 0 2px 4px rgba(0,0,0,0.05); }

.btn-group { display: flex; gap: 12px; }
.btn-h { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 24px; border-radius: 14px; font-size: 14px; font-weight: 800; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
.btn-h-back { background: #fff; color: #475569; border: 1.5px solid #e5e7eb; }
.btn-h-back:hover { background: #f8fafc; border-color: #cbd5e1; color: #111827; }
</style>

<div class="history-container">
    {{-- Header Section --}}
    <div class="history-header">
        <div class="history-id-block">
            <span class="history-id-label">Electronic Transaction Record</span>
            <span class="history-id-val">INV #{{ $invoice->invoice_no }}</span>
        </div>
        <div class="btn-group">
            <a href="{{ route('invoices.index') }}" class="btn-h btn-h-back">
                <i data-lucide="arrow-left"></i> Back To History
            </a>
            {{-- USER DEMAND: NO PRINTING BUTTONS --}}
        </div>
    </div>

    {{-- High-Level Stats --}}
    <div class="history-stats-row">
        <div class="h-stat-card">
            <span class="h-stat-label">Customer Name</span>
            <span class="h-stat-val">{{ $customerName }}</span>
        </div>
        <div class="h-stat-card">
            <span class="h-stat-label">Transaction Date</span>
            <span class="h-stat-val">{{ $invoice->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="h-stat-card">
            <span class="h-stat-label">Staff Member</span>
            <span class="h-stat-val">{{ $staffName }}</span>
        </div>
        <div class="h-stat-card">
            <span class="h-stat-label">Compliance</span>
            <span class="h-stat-val" style="color:{{ $fbrSync==='SUCCESS' ? '#16a34a' : '#f59e0b' }}">{{ $fbrSync }}</span>
        </div>
    </div>

    <div class="history-content-grid">
        {{-- Itemized List --}}
        <div class="history-main-card">
            <div class="h-card-head">Detailed Order Items</div>
            <div class="h-card-body" style="padding:0">
                <table class="h-table">
                    <thead>
                        <tr>
                            <th style="width:60px; padding-left:30px">#</th>
                            <th>Description</th>
                            <th style="text-align:right">Quantity</th>
                            <th style="text-align:right">Unit Price</th>
                            <th style="text-align:right; padding-right:30px">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $idx => $item)
                        <tr>
                            <td style="padding-left:30px; font-weight:700; color:#94a3b8;">{{ str_pad($idx+1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="item-desc">
                                    <span class="item-primary">{{ $item->itemizable->name ?? 'Product Item' }}</span>
                                    <span class="item-secondary">{{ class_basename($item->itemizable_type) }} • Taxable 5%</span>
                                </div>
                            </td>
                            <td style="text-align:right; font-weight:700;">{{ $item->quantity }}</td>
                            <td style="text-align:right; color:#64748b;">PKR {{ number_format($item->price, 2) }}</td>
                            <td style="text-align:right; font-weight:900; color:#111827; padding-right:30px">PKR {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Final Calculations --}}
        <div style="display:flex; flex-direction:column; gap:30px">
            <div class="history-main-card">
                <div class="h-card-head">Financial Summary</div>
                <div class="h-card-body">
                    <div class="h-summary-box">
                        <div class="h-sum-row">
                            <span>Gross Amount</span>
                            <b>PKR {{ number_format($invoice->total_amount, 2) }}</b>
                        </div>
                        <div class="h-sum-row">
                            <span>Service Tax (GST 5%)</span>
                            <b>PKR {{ number_format($invoice->tax, 2) }}</b>
                        </div>
                        @if($invoice->discount > 0)
                        <div class="h-sum-row">
                            <span>Discount Applied</span>
                            <b style="color:#ef4444;">- PKR {{ number_format($invoice->discount, 2) }}</b>
                        </div>
                        @endif
                        <div class="h-sum-total">
                            <span class="h-total-label">Payable Amount</span>
                            <span class="h-total-val">PKR {{ number_format($invoice->payable_amount, 2) }}</span>
                        </div>
                    </div>

                    <div style="margin-top:25px; padding-top:20px; border-top:1px solid #f1f5f9;">
                        <div class="h-sum-row" style="margin-bottom:8px">
                            <span>Payment Method</span>
                            <span style="font-weight:900; color:#111827; text-transform:uppercase;">{{ $invoice->payment_method ?? 'cash' }}</span>
                        </div>
                        <div class="h-sum-row">
                            <span>Transaction Status</span>
                            <span style="font-weight:900; color:#16a34a; text-transform:uppercase;">VERIFIED PAID</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="history-main-card" style="background:#111827; border:none;">
                <div class="h-card-body" style="color:#fff; text-align:center;">
                    <i data-lucide="shield-check" style="width:40px; height:40px; color:#F7DF79; margin-bottom:12px;"></i>
                    <div style="font-size:14px; font-weight:800; letter-spacing:0.05em; text-transform:uppercase; margin-bottom:6px;">Administrative Record</div>
                    <p style="font-size:12px; color:#94a3b8; line-height:1.6; margin:0;">
                        This digital record was generated by the internal POS system and represents the finalized fiscal data for this transaction.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
