@extends('layouts.app')
@section('hide-header')@endsection
@section('content')
<style>
*{box-sizing:border-box}
body{font-family:'Outfit',sans-serif}
.pay-page{padding:18px;display:flex;flex-direction:column;gap:14px;background:linear-gradient(145deg,#f6f8fc 0%,#edf2f7 55%,#eaf0f8 100%);min-height:100vh}
.pay-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.pay-title{font-size:22px;font-weight:900;color:#111827}
.pay-sub{font-size:12px;color:#94a3b8;margin-top:3px}
.back-btn{display:inline-flex;align-items:center;gap:6px;height:38px;padding:0 14px;border:1px solid #e5e7eb;background:#fff;border-radius:10px;color:#475569;font-size:12px;font-weight:800;text-decoration:none}
.back-btn:hover{border-color:#cbd5e1;color:#111827}
.pay-grid{display:grid;grid-template-columns:1.3fr .9fr;gap:14px}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 2px 8px rgba(15,23,42,.05)}
.card-head{padding:14px 16px;border-bottom:1px solid #f1f5f9}
.card-title{font-size:12px;font-weight:900;color:#111827;text-transform:uppercase;letter-spacing:.08em}
.card-body{padding:14px 16px}
.items{display:flex;flex-direction:column;gap:8px;max-height:460px;overflow-y:auto}
.item{display:flex;align-items:center;justify-content:space-between;gap:10px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:10px}
.item-name{font-size:13px;font-weight:700;color:#0f172a}
.item-meta{font-size:11px;color:#64748b}
.item-price{font-size:13px;font-weight:900;color:#111827}
.field{display:flex;flex-direction:column;gap:6px;margin-bottom:10px}
.field label{font-size:10px;font-weight:900;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em}
.field input{height:38px;border:1.5px solid #e5e7eb;border-radius:10px;padding:0 12px;font-size:13px;font-family:'Outfit',sans-serif;outline:none}
.field input:focus{border-color:#F7DF79;box-shadow:0 0 0 3px rgba(247,223,121,.18)}
.totals{display:flex;flex-direction:column;gap:8px;margin-top:4px}
.tot-row{display:flex;justify-content:space-between;font-size:13px;color:#64748b}
.tot-row b{color:#111827}
.tot-total{display:flex;justify-content:space-between;align-items:center;border-top:1px solid #e5e7eb;padding-top:10px;margin-top:2px;font-size:20px;font-weight:900;color:#111827}
.methods{display:grid;grid-template-columns:repeat(4,1fr);gap:6px;margin-top:8px}
.m-btn{border:1.5px solid #e5e7eb;background:#fff;border-radius:10px;padding:8px 4px;display:flex;flex-direction:column;align-items:center;gap:3px;cursor:pointer}
.m-btn i{width:15px;height:15px;color:#94a3b8}
.m-btn span{font-size:9px;font-weight:900;color:#94a3b8;text-transform:uppercase}
.m-btn.active{border-color:#facc15;background:#fef9c3}
.m-btn.active i,.m-btn.active span{color:#92400e}
.panel{background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:10px;margin-top:8px}
.panel-row{display:flex;justify-content:space-between;align-items:center;gap:8px}
.panel-row input{width:110px;height:34px;border:1px solid #dbe2ea;border-radius:8px;padding:0 10px;text-align:right}
.panel-note{font-size:11px;font-weight:800;margin-top:8px;text-align:right}
.checkout-btn{width:100%;height:44px;border:none;border-radius:12px;background:#F7DF79;color:#111827;font-size:13px;font-weight:900;letter-spacing:.06em;cursor:pointer;margin-top:10px;box-shadow:0 4px 14px rgba(247,223,121,.35)}
.checkout-btn:disabled{opacity:.45;cursor:not-allowed}
@media (max-width: 1024px){.pay-grid{grid-template-columns:1fr}}
</style>

<div class="pay-page">
  <div class="pay-head">
    <div>
      <div class="pay-title">Payment</div>
      <div class="pay-sub">Complete billing and checkout</div>
    </div>
    <a href="{{ route('pos.index') }}" class="back-btn"><i data-lucide="arrow-left"></i> Back To POS</a>
  </div>

  <div class="pay-grid">
    <div class="card">
      <div class="card-head"><div class="card-title">Order Items</div></div>
      <div class="card-body">
        <div class="field">
          <label>Customer Name</label>
          <input id="customer-name" type="text" placeholder="Walk-in Customer">
        </div>
        <div id="order-items" class="items"></div>
      </div>
    </div>

    <div class="card">
      <div class="card-head"><div class="card-title">Payment Summary</div></div>
      <div class="card-body">
        <div class="field">
          <label>Discount (PKR)</label>
          <input id="manual-discount" type="number" step="1" placeholder="0">
        </div>
        <div class="totals">
          <div class="tot-row"><span>Subtotal</span><b id="label-subtotal">PKR 0.00</b></div>
          <div class="tot-row"><span>Tax (5%)</span><b id="label-tax">PKR 0.00</b></div>
          <div class="tot-row"><span>Discount</span><b id="label-discount">-PKR 0.00</b></div>
          <div class="tot-total"><span>Total</span><span id="label-total">PKR 0.00</span></div>
        </div>

        <div style="margin-top:10px">
          <div style="font-size:10px;font-weight:900;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em">Payment Method</div>
          <div class="methods">
            <button class="m-btn active" data-method="cash" onclick="setPayment('cash')"><i data-lucide="banknote"></i><span>Cash</span></button>
            <button class="m-btn" data-method="card" onclick="setPayment('card')"><i data-lucide="credit-card"></i><span>Card</span></button>
            <!-- <button class="m-btn" data-method="bank" onclick="setPayment('bank')"><i data-lucide="landmark"></i><span>Bank</span></button> -->
            <button class="m-btn" data-method="split" onclick="setPayment('split')"><i data-lucide="arrow-left-right"></i><span>Split</span></button>
          </div>
        </div>

        <div id="cash-panel" class="panel">
          <div class="panel-row"><label style="font-size:12px;font-weight:700;color:#475569">Cash Tendered</label><input id="cash-tendered" type="number" placeholder="0.00" oninput="updateCashChange()"></div>
          <div id="cash-change" class="panel-note" style="color:#16a34a">Change: PKR 0.00</div>
        </div>

        <div id="split-panel" class="panel" style="display:none">
          <div class="panel-row"><label style="font-size:12px;font-weight:700;color:#475569">Cash</label><input id="split-cash" type="number" placeholder="0.00" oninput="updateSplitRemaining()"></div>
          <div class="panel-row" style="margin-top:8px"><label style="font-size:12px;font-weight:700;color:#475569">Card</label><input id="split-card" type="number" placeholder="0.00" oninput="updateSplitRemaining()"></div>
          <div id="split-remaining" class="panel-note" style="color:#ef4444">Remaining: PKR 0.00</div>
        </div>

        <button id="checkout-btn" class="checkout-btn" onclick="checkout()">PROCEED TO CHECKOUT</button>
        <button id="done-btn" class="checkout-btn" style="display:none; background:#16a34a; color:#fff;" onclick="window.location.href='{{ route('pos.index') }}'">DONE & BACK TO POS</button>
        
        <!-- Hidden iframe for instant printing on the same page -->
        <iframe id="receipt-iframe" style="position: absolute; width: 0; height: 0; border: none; visibility: hidden;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
let cart = [];
let paymentMethod = 'cash';
let manualDiscount = 0;

document.addEventListener('DOMContentLoaded', () => {
  const payloadRaw = sessionStorage.getItem('pos_payment_payload');
  if (!payloadRaw) {
    window.location.href = '{{ route("pos.index") }}';
    return;
  }
  const payload = JSON.parse(payloadRaw);
  cart = payload.items || [];
  document.getElementById('customer-name').value = payload.customer_name || 'Walk-in Customer';
  document.getElementById('manual-discount').addEventListener('input', e => {
    manualDiscount = parseFloat(e.target.value) || 0;
    updateTotals();
  });
  renderItems();
  updateTotals();
  lucide.createIcons();
});

function renderItems() {
  const wrap = document.getElementById('order-items');
  if (!cart.length) {
    wrap.innerHTML = '<div style="padding:16px;text-align:center;color:#94a3b8;font-weight:700">No items in order.</div>';
    return;
  }
  wrap.innerHTML = cart.map(item => `
    <div class="item">
      <div>
        <div class="item-name">${item.name}</div>
        <div class="item-meta">${item.quantity} x PKR ${Number(item.price).toFixed(2)}</div>
      </div>
      <div class="item-price">PKR ${(item.price * item.quantity).toFixed(2)}</div>
    </div>
  `).join('');
}

function updateTotals() {
  const sub = cart.reduce((a, i) => a + (i.price * i.quantity), 0);
  const tax = sub * 0.05;
  const disc = manualDiscount;
  const total = Math.max(0, sub + tax - disc);
  document.getElementById('label-subtotal').innerText = `PKR ${sub.toFixed(2)}`;
  document.getElementById('label-tax').innerText = `PKR ${tax.toFixed(2)}`;
  document.getElementById('label-discount').innerText = `-PKR ${disc.toFixed(2)}`;
  document.getElementById('label-total').innerText = `PKR ${total.toFixed(2)}`;
  if (paymentMethod === 'cash') updateCashChange();
  if (paymentMethod === 'split') updateSplitRemaining();
}

function setPayment(method) {
  paymentMethod = method;
  document.querySelectorAll('.m-btn').forEach(b => b.classList.toggle('active', b.dataset.method === method));
  document.getElementById('cash-panel').style.display = method === 'cash' ? 'block' : 'none';
  document.getElementById('split-panel').style.display = method === 'split' ? 'block' : 'none';
  if (method === 'cash') updateCashChange();
  if (method === 'split') updateSplitRemaining();
}

function getTotal() {
  const sub = cart.reduce((a, i) => a + (i.price * i.quantity), 0);
  const tax = sub * 0.05;
  return Math.max(0, sub + tax - manualDiscount);
}

function updateCashChange() {
  const total = getTotal();
  const tendered = parseFloat(document.getElementById('cash-tendered').value) || 0;
  const change = tendered - total;
  const el = document.getElementById('cash-change');
  if (change >= 0) {
    el.innerText = `Change: PKR ${change.toFixed(2)}`;
    el.style.color = '#16a34a';
  } else {
    el.innerText = `Need: PKR ${Math.abs(change).toFixed(2)}`;
    el.style.color = '#ef4444';
  }
}

function updateSplitRemaining() {
  const total = getTotal();
  const cash = parseFloat(document.getElementById('split-cash').value) || 0;
  const card = parseFloat(document.getElementById('split-card').value) || 0;
  const rem = total - cash - card;
  const el = document.getElementById('split-remaining');
  el.innerText = rem <= 0.001 ? `Fully Paid (PKR ${total.toFixed(2)})` : `Remaining: PKR ${rem.toFixed(2)}`;
  el.style.color = rem <= 0.001 ? '#16a34a' : '#ef4444';
}

async function checkout() {
  if (!cart.length) return;
  const sub = cart.reduce((a, i) => a + (i.price * i.quantity), 0);
  const tax = sub * 0.05;
  const disc = manualDiscount;
  const total = Math.max(0, sub + tax - disc);
  if (paymentMethod === 'split') {
    const splitCash = parseFloat(document.getElementById('split-cash').value) || 0;
    const splitCard = parseFloat(document.getElementById('split-card').value) || 0;
    if (splitCash + splitCard < total - 0.01) {
      showAppMessage('Split amounts must cover total.', 'error');
      return;
    }
  }
  if (paymentMethod === 'cash') {
    const tendered = parseFloat(document.getElementById('cash-tendered').value) || 0;
    if (tendered < total - 0.01) {
      showAppMessage('Insufficient cash tendered.', 'error');
      return;
    }
  }

  const btn = document.getElementById('checkout-btn');
  btn.disabled = true;
  btn.innerText = 'PROCESSING...';
  const customer = document.getElementById('customer-name').value.trim() || 'Walk-in Customer';
  let tenderedAmt = total;
  if (paymentMethod === 'cash') tenderedAmt = parseFloat(document.getElementById('cash-tendered').value) || total;
  if (paymentMethod === 'split') tenderedAmt = parseFloat(document.getElementById('split-cash').value) || 0;

  try {
    const res = await fetch('{{ route("pos.store") }}', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
      body: JSON.stringify({
        items: cart,
        payment_method: paymentMethod,
        total_amount: sub,
        tax,
        discount: disc,
        payable_amount: total,
        tendered_amount: tenderedAmt,
        customer_id: null,
        customer_name: customer
      })
    });
    const data = await res.json();
    if (!data.success) throw new Error(data.message || 'Checkout failed');
    sessionStorage.removeItem('pos_payment_payload');
    
    btn.style.display = 'none';
    document.getElementById('done-btn').style.display = 'block';

    // Hook auto-redirection into the print process
    const iframe = document.getElementById('receipt-iframe');
    iframe.onload = function() {
      // The browser's print dialog will naturally freeze this timer. 
      // Once the cashier completes the print, the timer finishes and auto-routes back!
      setTimeout(() => {
        window.location.href = '{{ route("pos.index") }}';
      }, 1500);
    };
    
    // Auto-Print the thermal receipt invisibly on this exact screen
    iframe.src = `/invoices/${data.invoice.id}`;
    
  } catch (e) {
    showAppMessage(e.message || 'Something went wrong.', 'error');
    btn.disabled = false;
    btn.innerText = 'PROCEED TO CHECKOUT';
  }
}
</script>
@endsection
