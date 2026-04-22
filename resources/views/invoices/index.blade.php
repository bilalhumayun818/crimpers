@extends('layouts.app')

@section('page-title', 'Sale History')
@section('page-sub', 'Filter and browse all transactions.')

@section('content')

<style>
.inv-wrap{font-family:"Outfit",sans-serif;display:flex;flex-direction:column;gap:20px}

/* Stats */
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.stat-card{border-radius:18px;padding:20px 22px;border:1px solid transparent;transition:transform .2s}
.stat-card:hover{transform:translateY(-2px)}
.stat-card.yellow{background:#fef9c3;border-color:#fef08a}
.stat-card.white{background:#fff;border-color:#e5e7eb}
.stat-card.purple{background:#f3e8ff;border-color:#e9d5ff}
.stat-label{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px}
.stat-card.yellow .stat-label{color:#92400e}
.stat-card.white .stat-label{color:#9ca3af}
.stat-card.purple .stat-label{color:#7c3aed}
.stat-val{font-size:26px;font-weight:900;color:#111827;line-height:1}
.stat-note{font-size:11px;font-weight:500;margin-top:6px}
.stat-card.yellow .stat-note{color:#ca8a04}
.stat-card.white .stat-note{color:#9ca3af}
.stat-card.purple .stat-note{color:#9333ea}

/* Filter Card */
.filter-card{background:#fff;border-radius:18px;border:1px solid #e5e7eb;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow:hidden}
.filter-head{padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.filter-head-title{font-size:13px;font-weight:800;color:#111827;display:flex;align-items:center;gap:8px}
.filter-head-title svg{width:15px;height:15px;color:#9ca3af}
.filter-toggle{font-size:11px;font-weight:700;color:#9ca3af;background:none;border:none;cursor:pointer;font-family:"Outfit",sans-serif;transition:color .2s}
.filter-toggle:hover{color:#111827}
.filter-body{padding:16px 20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:5px}
.filter-field input,.filter-field select{width:100%;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 12px;font-size:13px;font-weight:400;font-family:"Outfit",sans-serif;color:#111827;outline:none;transition:all .2s}
.filter-field input:focus,.filter-field select:focus{border-color:#F7DF79;background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.15)}
.filter-actions{display:flex;align-items:flex-end;gap:8px}
.btn-filter{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;border:none;background:#F7DF79;font-size:13px;font-weight:800;color:#111827;cursor:pointer;font-family:"Outfit",sans-serif;transition:all .2s;box-shadow:0 4px 10px rgba(247,223,121,.35);white-space:nowrap}
.btn-filter:hover{background:#fde047;transform:translateY(-1px)}
.btn-filter svg{width:14px;height:14px}
.btn-reset{display:inline-flex;align-items:center;gap:6px;padding:9px 14px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;font-size:13px;font-weight:700;color:#6b7280;cursor:pointer;font-family:"Outfit",sans-serif;transition:all .2s;text-decoration:none;white-space:nowrap}
.btn-reset:hover{border-color:#9ca3af;color:#111827}

/* Active filters */
.active-filters{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:10px 20px;border-top:1px solid #f3f4f6;background:#fafafa}
.filter-chip{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;background:#fef9c3;border:1px solid #fde68a;border-radius:20px;font-size:11px;font-weight:700;color:#92400e}
.filter-chip svg{width:12px;height:12px;cursor:pointer;color:#ca8a04}
.filter-chip svg:hover{color:#ef4444}

/* Table */
.table-card{background:#fff;border-radius:18px;border:1px solid #e5e7eb;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow:hidden}
.table-toolbar{padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.table-toolbar-left{display:flex;align-items:center;gap:10px}
.table-count{font-size:13px;font-weight:700;color:#111827}
.table-count span{color:#9ca3af;font-weight:500}
.table-search{position:relative}
.table-search input{background:#f3f4f6;border:none;border-radius:10px;padding:8px 14px 8px 34px;font-size:13px;font-weight:400;font-family:"Outfit",sans-serif;outline:none;width:200px;transition:box-shadow .2s}
.table-search input:focus{box-shadow:0 0 0 2px rgba(247,223,121,.5);background:#fff}
.table-search svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af;pointer-events:none}
.btn-export{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;border:none;background:#111827;font-size:12px;font-weight:700;color:#fff;cursor:pointer;font-family:"Outfit",sans-serif;transition:all .2s;text-decoration:none}
.btn-export:hover{background:#1f2937}
.btn-export svg{width:14px;height:14px}

/* Table */
.inv-table{width:100%;border-collapse:collapse}
.inv-table thead tr{background:#f9fafb;border-bottom:1px solid #f1f1f1}
.inv-table th{padding:11px 18px;font-size:10px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;text-align:left;white-space:nowrap}
.inv-table th:last-child{text-align:right}
.inv-table tbody tr{border-bottom:1px solid #f9fafb;transition:background .15s}
.inv-table tbody tr:last-child{border-bottom:none}
.inv-table tbody tr:hover{background:#fafafa}
.inv-table td{padding:13px 18px;vertical-align:middle}
.inv-no{font-size:13px;font-weight:900;color:#111827}
.cust-name{font-size:13px;font-weight:700;color:#111827}
.cust-staff{font-size:11px;color:#9ca3af;font-weight:400;margin-top:1px}
.inv-date{font-size:12px;color:#6b7280;font-weight:400}
.pay-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;background:#f3f4f6;border:1px solid #e5e7eb;font-size:10px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em}
.pay-badge svg{width:12px;height:12px;color:#6b7280}
.inv-amount{font-size:14px;font-weight:900;color:#111827}
.fbr-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;cursor:pointer;border:none;font-family:"Outfit",sans-serif;transition:opacity .2s}
.fbr-badge:hover{opacity:.8}
.fbr-simulated{background:#fef9c3;color:#92400e}
.fbr-success{background:#dcfce7;color:#166534}
.fbr-failed{background:#fee2e2;color:#991b1b}
.fbr-none{background:#f3f4f6;color:#9ca3af;cursor:default}
.row-actions{display:flex;align-items:center;justify-content:center;gap:8px}
.row-btn{height:28px;padding:0 12px;border-radius:8px;border:none;background:#f3f4f6;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;text-decoration:none;font-size:11px;font-weight:800;color:#4b5563;text-transform:uppercase;letter-spacing:0.05em;}
.row-btn:hover{background:#e5e7eb;color:#111827}
.row-btn.view{background:#fef9c3;color:#a16207}
.row-btn.view:hover{background:#fde047;color:#713f12}
.row-btn.print{background:#eff6ff;color:#1d4ed8}
.row-btn.print:hover{background:#dbeafe;color:#1e40af}
.table-footer{padding:14px 20px;border-top:1px solid #f3f4f6;background:#fafafa}
.empty-state{padding:60px 20px;display:flex;flex-direction:column;align-items:center;gap:12px}
.empty-icon{width:56px;height:56px;background:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center}
.empty-icon svg{width:26px;height:26px;color:#9ca3af}
.empty-text{font-size:14px;color:#9ca3af;font-weight:500}

/* FBR Modal */
.modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);z-index:100;display:flex;align-items:center;justify-content:center;padding:20px}
.modal-card{background:#fff;border-radius:20px;width:100%;max-width:640px;max-height:85vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.2)}
.modal-head{padding:18px 22px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.modal-head-title{font-size:16px;font-weight:900;color:#111827}
.modal-head-sub{font-size:11px;color:#9ca3af;margin-top:2px}
.modal-close{background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;border-radius:8px;transition:all .2s;display:flex}
.modal-close:hover{background:#f3f4f6;color:#111827}
.modal-close svg{width:18px;height:18px}
.modal-body{flex:1;overflow-y:auto;padding:22px}
.json-panel{background:#111827;color:#d1fae5;border-radius:14px;padding:20px;font-family:"Courier New",monospace;font-size:12px;line-height:1.7;overflow-x:auto}
.json-key{color:#93c5fd}.json-string{color:#86efac}.json-number{color:#fde68a}
.resp-box{margin-top:14px;padding:14px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;font-size:12px;color:#374151;font-family:monospace;word-break:break-all}
</style>
<div class="inv-wrap">

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card yellow">
            <div class="stat-label">Total Sales</div>
            <div class="stat-val">PKR {{ number_format($totalSales, 0) }}</div>
            <div class="stat-note">From {{ $totalInvoices }} transactions</div>
        </div>
        <div class="stat-card white">
            <div class="stat-label">Period Invoices</div>
            <div class="stat-val">{{ number_format($periodInvoices, 0) }}</div>
            <div class="stat-note">Volume in selected timeframe</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Avg Transaction</div>
            <div class="stat-val">PKR {{ $totalInvoices > 0 ? number_format($totalSales / $totalInvoices, 0) : 0 }}</div>
            <div class="stat-note">Customer spending power</div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <div class="filter-head">
            <div class="filter-head-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filter Invoices
            </div>
            <button class="filter-toggle" onclick="toggleFilters()">
                <span id="filter-toggle-text">{{ request()->hasAny(['date_from','date_to','search','payment_method','period']) ? 'Hide Filters' : 'Show Filters' }}</span>
            </button>
        </div>

        <form method="GET" action="{{ route('invoices.index') }}" id="filter-form">
            <div class="filter-body" id="filter-body" style="{{ request()->hasAny(['date_from','date_to','search','payment_method','period']) ? '' : 'display:none' }}">

                {{-- Date From --}}
                <div class="filter-field">
                    <label>From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>

                {{-- Date To --}}
                <div class="filter-field">
                    <label>To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>

                {{-- Quick Period --}}
                <div class="filter-field">
                    <label>Quick Period</label>
                    <select name="period">
                        <option value="">All Time</option>
                        <option value="today" {{ request('period')=='today'?'selected':'' }}>Today</option>
                        <option value="yesterday" {{ request('period')=='yesterday'?'selected':'' }}>Yesterday</option>
                        <option value="this_week" {{ request('period')=='this_week'?'selected':'' }}>This Week</option>
                        <option value="last_week" {{ request('period')=='last_week'?'selected':'' }}>Last Week</option>
                        <option value="this_month" {{ request('period')=='this_month'?'selected':'' }}>This Month</option>
                        <option value="last_month" {{ request('period')=='last_month'?'selected':'' }}>Last Month</option>
                        <option value="this_year" {{ request('period')=='this_year'?'selected':'' }}>This Year</option>
                    </select>
                </div>

                {{-- Customer Search --}}
                <div class="filter-field">
                    <label>Customer / Invoice #</label>
                    <input type="text" name="search" placeholder="Name or #INV..." value="{{ request('search') }}">
                </div>

                {{-- Payment Method --}}
                <div class="filter-field">
                    <label>Payment Method</label>
                    <select name="payment_method">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method')=='cash'?'selected':'' }}>Cash</option>
                        <option value="card" {{ request('payment_method')=='card'?'selected':'' }}>Card</option>
                        <option value="bank" {{ request('payment_method')=='bank'?'selected':'' }}>Bank</option>
                        <option value="split" {{ request('payment_method')=='split'?'selected':'' }}>Split</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Apply
                    </button>
                    <a href="{{ route('invoices.index') }}" class="btn-reset">Reset</a>
                </div>

            </div>

            {{-- Active filter chips --}}
            @if(request()->hasAny(['date_from','date_to','search','payment_method','period']))
            <div class="active-filters">
                <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em">Active:</span>
                @if(request('date_from'))
                <span class="filter-chip">From: {{ request('date_from') }}</span>
                @endif
                @if(request('date_to'))
                <span class="filter-chip">To: {{ request('date_to') }}</span>
                @endif
                @if(request('period'))
                <span class="filter-chip">{{ ucfirst(str_replace('_',' ',request('period'))) }}</span>
                @endif
                @if(request('search'))
                <span class="filter-chip">Search: "{{ request('search') }}"</span>
                @endif
                @if(request('payment_method'))
                <span class="filter-chip">{{ ucfirst(request('payment_method')) }}</span>
                @endif
            </div>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-toolbar">
            <div class="table-toolbar-left">
                <span class="table-count">{{ $invoices->total() }} <span>invoices found</span></span>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <div class="table-search">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" id="inv-search" placeholder="Quick search…" oninput="filterTable(this.value)">
                </div>
                <form action="{{ route('invoices.export') }}" method="GET" style="display:contents">
                    <input type="hidden" name="format" value="pdf">
                    <button type="submit" class="btn-export">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        Export PDF
                    </button>
                </form>
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Date & Time</th>
                        <th>Payment</th>
                        <th>Amount</th>
                        <th>FBR</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody id="inv-tbody">
                    @forelse($invoices as $invoice)
                    <tr class="inv-row">
                        <td><span class="inv-no">#{{ $invoice->invoice_no }}</span></td>
                        <td>
                            <div class="cust-name">{{ $invoice->customer ? $invoice->customer->name : ($invoice->customer_name ?? 'Walk-in') }}</div>
                            <div class="cust-staff">by {{ $invoice->user->name ?? 'System' }}</div>
                        </td>
                        <td>
                            <div class="inv-date">{{ $invoice->created_at->format('d M Y') }}</div>
                            <div class="inv-date">{{ $invoice->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            @php $icons=['cash'=>'banknote','card'=>'credit-card','bank'=>'landmark','split'=>'split']; $icon=$icons[strtolower($invoice->payment_method??'cash')]??'banknote'; @endphp
                            <div class="pay-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    @if($icon==='banknote')<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/>
                                    @elseif($icon==='credit-card')<rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                                    @elseif($icon==='landmark')<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                                    @else<rect width="20" height="12" x="2" y="6" rx="2"/>
                                    @endif
                                </svg>
                                {{ ucfirst($invoice->payment_method ?? 'Cash') }}
                            </div>
                        </td>
                        <td><span class="inv-amount">PKR {{ number_format($invoice->payable_amount, 0) }}</span></td>
                        <td>
                            @if($invoice->fbrLog)
                                <button class="fbr-badge fbr-{{ $invoice->fbrLog->status }}">{{ ucfirst($invoice->fbrLog->status) }}</button>
                            @else
                                <span class="fbr-badge fbr-none">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('sales-history.show', $invoice) }}" class="row-btn view" title="View History">
                                    View History
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                            <p class="empty-text">No invoices found for the selected filters.</p>
                            <a href="{{ route('invoices.index') }}" style="font-size:13px;color:#F7DF79;font-weight:700;text-decoration:none">Clear filters</a>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">{{ $invoices->links() }}</div>
    </div>
</div>
{{-- FBR Modal --}}
<div id="fbr-modal" class="modal-bg" style="display:none" onclick="if(event.target===this)closeFbrModal()">
    <div class="modal-card">
        <div class="modal-head">
            <div><div class="modal-head-title">FBR Integration Details</div><div id="fbr-modal-inv" class="modal-head-sub"></div></div>
            <button onclick="closeFbrModal()" class="modal-close"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <div class="modal-body">
            <p style="font-size:10px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">JSON Payload</p>
            <div class="json-panel" id="fbr-modal-content"></div>
            <p style="font-size:10px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin:14px 0 8px">FBR Response</p>
            <div class="resp-box" id="fbr-modal-response"></div>
        </div>
    </div>
</div>

@php
    $fbrData = $invoices->filter(fn($i) => $i->fbrLog)->mapWithKeys(fn($i) => [
        $i->fbrLog->id => ["invoice_no"=>$i->invoice_no,"payload"=>$i->fbrLog->payload,"response"=>$i->fbrLog->response]
    ])->toJson();
@endphp

<script>
const fbrLogs={!! $fbrData !!};
function filterTable(val){
    val=val.toLowerCase();
    document.querySelectorAll(".inv-row").forEach(row=>{row.style.display=row.innerText.toLowerCase().includes(val)?"":"none";});
}
function toggleFilters(){
    const body=document.getElementById("filter-body");
    const txt=document.getElementById("filter-toggle-text");
    const hidden=body.style.display==="none";
    body.style.display=hidden?"grid":"none";
    txt.innerText=hidden?"Hide Filters":"Show Filters";
}
function showFbrPayload(id){
    const log=fbrLogs[id];if(!log)return;
    document.getElementById("fbr-modal-inv").innerText="Invoice #"+log.invoice_no;
    document.getElementById("fbr-modal-content").innerHTML=syntaxHighlight(JSON.stringify(log.payload,null,2));
    document.getElementById("fbr-modal-response").innerText=log.response||"No response recorded";
    document.getElementById("fbr-modal").style.display="flex";
}
function closeFbrModal(){document.getElementById("fbr-modal").style.display="none";}
function syntaxHighlight(json){
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,m=>{
        let c="json-number";
        if(/^"/.test(m)){c=/:$/.test(m)?"json-key":"json-string";}
        return`<span class="${c}">${m}</span>`;
    });
}
document.addEventListener("keydown",e=>{if(e.key==="Escape")closeFbrModal();});
</script>
@endsection
