@extends('layouts.app')

@section('page-title', 'POS Terminal')
@section('page-sub', 'Fast billing for services and packages')

@section('content')
  <div style="padding:24px 28px 16px;background:#f1f5f9">
    <h2 style="font-size:22px;font-weight:900;color:#111827;margin:0;line-height:1.2">POS Terminal</h2>
    <p style="font-size:13px;color:#9ca3af;margin:4px 0 0;font-weight:500">Fast billing for services and packages</p>
  </div>
  <style>
    * {
      box-sizing: border-box
    }

    body {
      font-family: 'Outfit', sans-serif
    }

    .pos-page {
      display: flex;
      overflow: hidden;
      background: #f8fafc;
      gap: 0;
      height: 100vh; /* Absolute Max Height */
    }

    /* ── MIDDLE PANEL ── */
    .mid-panel {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      min-width: 0
    }

    /* Top bar */
    .pos-topbar {
      background: #fff;
      border-radius: 12px;
      margin: 12px 16px 0 16px;
      flex-shrink: 0;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05)
    }

    .pos-topbar-controls {
      padding: 8px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px
    }

    .pos-heading {
      display: flex;
      flex-direction: column;
      gap: 1px
    }

    .pos-title {
      font-size: 15px;
      font-weight: 900;
      color: #111827
    }

    .pos-subtitle {
      font-size: 10px;
      color: #94a3b8;
      font-weight: 600
    }

    .pos-tabs {
      display: flex;
      gap: 3px;
      background: #f3f4f6;
      border-radius: 10px;
      padding: 3px
    }

    .pos-tab {
      padding: 7px 16px;
      border-radius: 8px;
      border: none;
      background: transparent;
      font-size: 12px;
      font-weight: 700;
      color: #6b7280;
      cursor: pointer;
      transition: all .2s;
      font-family: 'Outfit', sans-serif
    }

    .pos-tab.active {
      background: #fff;
      color: #111827;
      box-shadow: 0 1px 4px rgba(0, 0, 0, .1)
    }

    .pos-search {
      position: relative;
      width: 280px
    }

    .pos-search input {
      width: 100%;
      background: #f3f4f6;
      border: 1.5px solid transparent;
      border-radius: 10px;
      padding: 8px 14px 8px 36px;
      font-size: 13px;
      font-family: 'Outfit', sans-serif;
      outline: none;
      transition: all .2s
    }

    .pos-topbar {
      background: #fff;
      border-radius: 12px;
      margin: 12px 16px 0 16px;
      flex-shrink: 0;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 12px;
      z-index: 20;
    }

    .pos-topbar-controls {
      padding: 8px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px
    }

    .hcat-wrap {
      display: flex;
      align-items: flex-start;
      background: #fff;
      border-radius: 12px;
      margin: 12px 16px 0;
      padding: 6px 12px;
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 72px; /* Adjusted below topbar */
      z-index: 15;
    }

    .hcat-list {
      flex: 1;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      overflow: hidden;
      max-height: 44px;
      transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .hcat-wrap.expanded .hcat-list {
      max-height: 800px;
    }

    .hcat-btn {
      padding: 10px 24px;
      height: 42px;
      border: 1.5px solid #e5e7eb;
      background: #fff;
      border-radius: 100px;
      font-size: 14px;
      font-weight: 700;
      color: #475569;
      cursor: pointer;
      transition: all .2s ease;
      font-family: 'Outfit', sans-serif;
      white-space: nowrap;
      flex-shrink: 0;
    }
    
    .hcat-btn:hover {
      border-color: #F7DF79;
      background: #fffbeb;
      color: #1e293b;
    }

    .hcat-btn:hover {
      border-color: #cbd5e1;
      color: #111827;
      background: #fff;
    }

    .hcat-btn.active {
      background: #F7DF79;
      border-color: #F7DF79;
      color: #111827;
      box-shadow: 0 2px 8px rgba(247, 223, 121, .35);
    }

    .hcat-more-wrap {
      margin-left: 12px;
      flex-shrink: 0;
    }

    .hcat-more-btn {
      width: 34px;
      height: 34px;
      border-radius: 8px;
      border: 1.5px solid #e5e7eb;
      background: #f9fafb;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all .2s;
    }

    .hcat-more-btn:hover {
      border-color: #cbd5e1;
      background: #fff;
    }

    .hcat-menu {
      display: none;
      position: absolute;
      top: calc(100% + 4px);
      right: 0;
      width: 180px;
      background: #fff;
      border: 1.5px solid #e5e7eb;
      border-radius: 12px;
      padding: 6px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      z-index: 100;
      flex-direction: column;
      gap: 4px;
      max-height: 260px;
      overflow-y: auto;
    }

    .hcat-menu.show {
      display: flex;
    }

    .hcat-menu::-webkit-scrollbar {
      width: 4px;
    }

    .hcat-menu::-webkit-scrollbar-thumb {
      background: #e2e8f0;
      border-radius: 999px;
    }

    .hcat-menu-item {
      text-align: left;
      padding: 8px 12px;
      border: none;
      background: transparent;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      color: #475569;
      cursor: pointer;
      transition: all .2s;
      font-family: 'Outfit', sans-serif;
    }

    .hcat-menu-item:hover {
      background: #f8fafc;
      color: #111827;
    }

    .hcat-menu-item.active {
      background: #F7DF79;
      color: #111827;
    }

    /* Items grid */
    .items-area {
      flex: 1;
      overflow-y: auto;
      padding: 16px 20px 20px;
      background: #f0f2f5
    }

    .items-area::-webkit-scrollbar {
      width: 4px
    }

    .items-area::-webkit-scrollbar-thumb {
      background: #d1d5db;
      border-radius: 4px
    }

    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 12px
    }

    .items-grid-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px
    }

    .items-grid-title {
      font-size: 13px;
      font-weight: 800;
      color: #374151
    }

    .items-grid-count {
      font-size: 11px;
      font-weight: 600;
      color: #9ca3af
    }

    /* Item card */
    /* Item card - Two Tone (Half Color / Half White) */
    .item-card {
      border-radius: 16px;
      overflow: hidden;
      cursor: pointer;
      transition: all .25s ease;
      position: relative;
      border: 1px solid #e5e7eb;
      background: #fff; /* Bottom half white */
      box-shadow: 0 1px 4px rgba(0,0,0,0.03);
      display: flex;
      flex-direction: column;
    }

    .item-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.06);
      border-color: #d1d5db;
    }

    /* Top Half - Themed Background */
    .item-thumb {
      height: 85px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }
    
    .item-card[data-type="service"] .item-thumb { background: #fef9c3; }
    .item-card[data-type="package"] .item-thumb { background: #f3f4f6; }
    .item-card[data-type="product"] .item-thumb { background: #ecfeff; }

    .item-thumb i { width: 30px; height: 30px; opacity: 0.8; }
    .item-card[data-type="service"] .item-thumb i { color: #92400e; }
    .item-card[data-type="package"] .item-thumb i { color: #4b5563; }
    .item-card[data-type="product"] .item-thumb i { color: #0369a1; }

    /* Bottom Half - Pure White */
    .item-body { 
      padding: 12px 14px 14px; 
      background: #fff;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .item-badge {
      position: absolute;
      top: 8px;
      right: 8px;
      font-size: 8px;
      font-weight: 900;
      padding: 2px 8px;
      border-radius: 20px;
      text-transform: uppercase;
      letter-spacing: .06em;
      z-index: 1
    }
    .badge-hot { background: #F7DF79; color: #92400e }
    .badge-bundle { background: #374151; color: #fff }

    .item-name {
      font-size: 12px;
      font-weight: 800;
      color: #111827;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-bottom: 2px
    }
    .item-sub { font-size: 10px; color: #9ca3af; font-weight: 400; margin-bottom: 8px }

    .item-footer { display: flex; align-items: center; justify-content: space-between }

    .item-price { font-size: 13px; font-weight: 900; color: #111827 }
    .item-card[data-type="service"] .item-price { color: #92400e }
    .item-card[data-type="package"] .item-price { color: #374151 }

    .item-add {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      border: 1px solid #e5e7eb;
      background: #fff; /* Plus BG is now White */
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all .2s ease;
      flex-shrink: 0;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .item-add i { width: 14px; height: 14px; }
    .item-card[data-type="service"] .item-add i { color: #d97706 }
    .item-card[data-type="package"] .item-add i { color: #4b5563 }
    .item-card[data-type="product"] .item-add i { color: #0891b2 }
    .item-add:hover { 
      transform: scale(1.15) rotate(90deg); 
      border-color: #cbd5e1;
      box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }

    /* ── RIGHT ORDER PANEL ── */
    .order-panel {
      width: 400px;
      flex-shrink: 0;
      background: #f8fafc;
      border-left: 1px solid #e2e8f0;
      display: flex;
      flex-direction: column;
      height: 100%;
      overflow: hidden;
    }

    /* Scrollable content area */
    .order-scroll {
      flex: 1;
      overflow-y: auto;
      padding: 12px 12px 0;
      display: flex;
      flex-direction: column;
      gap: 10px;
      min-height: 0;
    }
    .order-scroll::-webkit-scrollbar { width: 4px }
    .order-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px }

    /* Pinned checkout button at bottom */
    .order-checkout-pin {
      flex-shrink: 0;
      padding: 8px 12px 10px;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
    }

    .order-section {
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,.04);
      overflow: hidden;
      flex-shrink: 0;
    }

    /* Cart Section - This one should grow but not too much */
    .order-section:first-child {
      flex-shrink: 0;
    }

    /* Totals Section */
    .order-section.order-foot {
      flex-shrink: 0;
    }

    .order-head {
      padding: 20px 20px 12px;
      border-bottom: 1px solid #f8fafc;
      flex-shrink: 0;
    }

    .order-head-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 6px
    }

    .order-title {
      font-size: 14px;
      font-weight: 900;
      color: #0f172a
    }

    .clear-btn {
      font-size: 10px;
      font-weight: 800;
      color: #d1d5db;
      background: none;
      border: none;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: .08em;
      transition: color .2s;
      font-family: 'Outfit', sans-serif
    }

    .clear-btn:hover {
      color: #ef4444
    }

    .walkin-input {
      width: 100%;
      background: #f9fafb;
      border: 1.5px solid #e5e7eb;
      border-radius: 10px;
      padding: 8px 12px 8px 34px;
      font-size: 12px;
      font-weight: 600;
      font-family: 'Outfit', sans-serif;
      outline: none;
      color: #374151;
      transition: all .2s
    }

    .walkin-input:focus {
      border-color: #F7DF79;
      box-shadow: 0 0 0 3px rgba(247, 223, 121, .15);
      background: #fff
    }

    .customer-tag {
      display: inline-flex;
      align-items: center;
      padding: 2px 8px;
      border-radius: 999px;
      background: #fef9c3;
      color: #92400e;
      font-size: 9px;
      font-weight: 800;
      letter-spacing: .04em;
      text-transform: uppercase;
      margin-left: 6px
    }

    /* Cart */
    .cart-body {
      padding: 8px 14px 12px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      flex-shrink: 0;
    }

    .cart-body::-webkit-scrollbar {
      width: 3px
    }

    .cart-body::-webkit-scrollbar-thumb {
      background: #e5e7eb;
      border-radius: 3px
    }

    .empty-cart {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 6px;
      opacity: .3;
      padding: 24px 0
    }

    .empty-cart i {
      width: 28px;
      height: 28px;
      color: #9ca3af
    }

    .empty-cart p {
      font-size: 11px;
      color: #9ca3af;
      font-weight: 600
    }

    .cart-item {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #f9fafb;
      border-radius: 10px;
      padding: 8px 10px;
      transition: background .15s;
      border: 1px solid transparent
    }

    .cart-item:hover {
      background: #fef9c3;
      border-color: #fde68a
    }

    .cart-item-info {
      flex: 1;
      min-width: 0
    }

    .cart-item-name {
      font-size: 12px;
      font-weight: 700;
      color: #111827;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis
    }

    .cart-item-price {
      font-size: 11px;
      font-weight: 800;
      color: #d97706
    }

    .qty-ctrl {
      display: flex;
      align-items: center;
      gap: 4px;
      background: #fff;
      border-radius: 8px;
      padding: 3px 6px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, .08);
      flex-shrink: 0;
      border: 1px solid #f1f1f1
    }

    .qty-btn {
      background: none;
      border: none;
      cursor: pointer;
      color: #d1d5db;
      padding: 0;
      line-height: 1;
      transition: color .2s
    }

    .qty-btn:hover {
      color: #111827
    }

    .qty-btn i {
      width: 11px;
      height: 11px
    }

    .qty-num-input {
      font-size: 12px;
      font-weight: 900;
      color: #111827;
      width: 32px;
      text-align: center;
      border: none;
      background: transparent;
      outline: none;
      -moz-appearance: textfield;
    }
    .qty-num-input::-webkit-outer-spin-button,
    .qty-num-input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Footer */
    .order-foot {
      flex-shrink: 0;
    }

    .order-foot-scroll {
      padding: 12px 14px 14px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .order-foot-btn {
      padding: 0;
    }

    .order-subtitle {
      font-size: 10px;
      font-weight: 800;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: .1em
    }

    .disc-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px
    }

    .disc-label {
      font-size: 10px;
      font-weight: 800;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: .08em
    }

    .disc-input {
      width: 85px;
      background: #fff;
      border: 1.5px solid #e5e7eb;
      border-radius: 10px;
      padding: 6px 10px;
      text-align: right;
      font-size: 13px;
      font-weight: 700;
      color: #ef4444;
      font-family: 'Outfit', sans-serif;
      outline: none;
      transition: border-color .2s
    }

    .disc-input:focus {
      border-color: #fca5a5
    }

    .totals-lines {
      display: flex;
      flex-direction: column;
      gap: 5px
    }

    .tot-row {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      color: #6b7280
    }

    .tot-row span:last-child {
      font-weight: 600;
      color: #374151
    }

    .tot-row.disc span:last-child {
      color: #ef4444
    }

    .tot-divider {
      height: 1px;
      background: #e5e7eb;
      margin: 2px 0
    }

    .tot-total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 12px;
      background: #f9fafb;
      border-radius: 10px;
      border: 1px solid #e5e7eb
    }

    .tot-total-label {
      font-size: 13px;
      font-weight: 900;
      color: #111827
    }

    .tot-total-val {
      font-size: 18px;
      font-weight: 900;
      color: #111827
    }

    .customer-chip {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fffbeb;
      border: 1px solid #fde68a;
      border-radius: 12px;
      padding: 10px 14px;
      gap: 8px;
      margin-bottom: 12px; /* Added margin here */
    }

    .customer-chip-lbl {
      font-size: 9px;
      font-weight: 900;
      color: #92400e;
      text-transform: uppercase;
      letter-spacing: .06em
    }

    .customer-chip-name {
      font-size: 12px;
      font-weight: 800;
      color: #111827;
      max-width: 160px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      text-align: right
    }

    /* Payment */
    .pay-label {
      font-size: 10px;
      font-weight: 800;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: .08em
    }

    .pay-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 5px;
      margin-top: 6px
    }

    .pay-btn {
      background: #fff;
      border: 1.5px solid #e5e7eb;
      border-radius: 10px;
      padding: 8px 4px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 3px;
      cursor: pointer;
      transition: all .2s;
      font-family: 'Outfit', sans-serif
    }

    .pay-btn i {
      width: 15px;
      height: 15px;
      color: #9ca3af
    }

    .pay-btn span {
      font-size: 9px;
      font-weight: 800;
      color: #9ca3af;
      text-transform: uppercase
    }

    .pay-btn:hover {
      border-color: #F7DF79;
      background: #fef9c3
    }

    .pay-btn.active {
      border-color: #F7DF79;
      background: #fef9c3;
      box-shadow: 0 2px 8px rgba(247, 223, 121, .35)
    }

    .pay-btn.active i,
    .pay-btn.active span {
      color: #92400e
    }

    /* Split */
    .split-panel {
      background: #f5f3ff;
      border: 1.5px solid #e9d5ff;
      border-radius: 12px;
      padding: 12px;
      flex-direction: column;
      gap: 8px
    }

    .split-title {
      font-size: 10px;
      font-weight: 900;
      color: #7c3aed;
      text-transform: uppercase;
      letter-spacing: .08em
    }

    .split-row {
      display: flex;
      align-items: center;
      justify-content: space-between
    }

    .split-row label {
      font-size: 12px;
      font-weight: 700;
      color: #374151
    }

    .split-row input {
      width: 85px;
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 5px 8px;
      text-align: right;
      font-size: 12px;
      font-weight: 700;
      font-family: 'Outfit', sans-serif;
      outline: none
    }

    .split-rem {
      font-size: 10px;
      font-weight: 900;
      text-align: right;
      padding-top: 6px;
      border-top: 1px solid #e9d5ff
    }

    /* Checkout */
    .checkout-btn {
      width: 100%;
      background: #F7DF79;
      border: none;
      border-radius: 12px;
      padding: 11px 16px;
      font-size: 12px;
      font-weight: 800;
      color: #111827;
      cursor: pointer;
      letter-spacing: .07em;
      transition: all .2s;
      box-shadow: 0 4px 12px rgba(247,223,121,.35);
      font-family: 'Outfit', sans-serif
    }

    .checkout-btn:hover:not(:disabled) {
      background: #fde047;
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(247,223,121,.45)
    }

    .checkout-btn:disabled {
      opacity: .4;
      cursor: not-allowed;
      transform: none
    }

    /* Modal */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .45);
      backdrop-filter: blur(4px);
      z-index: 200;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px
    }

    .modal-box {
      background: #fff;
      border-radius: 24px;
      width: 100%;
      max-width: 360px;
      padding: 32px;
      text-align: center;
      box-shadow: 0 25px 60px rgba(0, 0, 0, .2)
    }

    .modal-icon {
      width: 60px;
      height: 60px;
      background: #dcfce7;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 18px
    }

    .modal-icon i {
      width: 28px;
      height: 28px;
      color: #16a34a
    }

    .modal-title {
      font-size: 20px;
      font-weight: 900;
      color: #111827;
      margin-bottom: 4px
    }

    .modal-inv {
      font-size: 11px;
      font-weight: 700;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: .12em;
      margin-bottom: 22px
    }

    .modal-actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 18px
    }

    .modal-btn {
      width: 100%;
      padding: 13px;
      border-radius: 12px;
      border: none;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all .2s;
      font-family: 'Outfit', sans-serif
    }

    .modal-btn i {
      width: 15px;
      height: 15px
    }

    .modal-btn.print {
      background: #f3f4f6;
      color: #374151
    }

    .modal-btn.print:hover {
      background: #e5e7eb
    }

    .modal-btn.wa {
      background: #dcfce7;
      color: #16a34a
    }

    .modal-btn.wa:hover {
      background: #bbf7d0
    }

    .modal-new {
      font-size: 10px;
      font-weight: 900;
      color: #9ca3af;
      background: none;
      border: none;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: .15em;
      transition: color .2s;
      font-family: 'Outfit', sans-serif
    }

    .modal-new:hover {
      color: #111827
    }

    /* Toast Notifications */
    .pos-toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #fff;
      color: #111827;
      padding: 14px 20px;
      border-left: 4px solid #ef4444;
      border-radius: 8px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      font-family: 'Outfit', sans-serif;
      font-size: 13px;
      font-weight: 700;
      z-index: 9999;
      transform: translateX(120%);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .pos-toast.show {
      transform: translateX(0);
    }

    .pos-toast.success {
      border-left-color: #16a34a;
    }

    .pos-toast-icon {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .pos-toast.error .pos-toast-icon {
      color: #ef4444;
    }

    .pos-toast.success .pos-toast-icon {
      color: #16a34a;
    }
  </style>
  <div class="pos-page">

    {{-- Push content below the heading bar --}}
    <div style="display:flex;flex:1;overflow:hidden;width:100%">

      {{-- Middle Panel --}}
      <div class="mid-panel">

        {{-- Top Bar --}}
        {{-- Top Bar --}}
        <div class="pos-topbar">
          <div class="pos-topbar-controls">
            <div class="pos-tabs">
              <button onclick="switchTab('all')" id="tab-all" class="pos-tab active">All</button>
              <button onclick="switchTab('services')" id="tab-services" class="pos-tab">Services</button>
              <button onclick="switchTab('packages')" id="tab-packages" class="pos-tab">Packages</button>
              {{-- <button onclick="switchTab('products')" id="tab-products" class="pos-tab">Products</button> --}}
            </div>
            <div class="pos-search">
              <svg
                style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af;pointer-events:none"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.3-4.3" />
              </svg>
              <input id="item-search" type="text" placeholder="Search items or barcode…" autofocus>
            </div>
          </div>
        </div>
        <div class="hcat-wrap">
          <div id="hcat-list" class="hcat-list">
            <button onclick="filterCategory('all')" class="hcat-btn active" data-cat="all">All Categories</button>
            @foreach($categories as $cat)
              <button onclick="filterCategory('{{ $cat->id }}')" class="hcat-btn" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
          </div>
          <div id="hcat-more-wrap" class="hcat-more-wrap">
            <button onclick="toggleExpandLayout()" class="hcat-more-btn">
              <i data-lucide="chevron-down" style="width:20px;height:20px;color:#6b7280"></i>
            </button>
          </div>
        </div>

        {{-- Items Grid --}}
        <div class="items-area">
          <div class="items-grid-head">
            <div class="items-grid-title">Available Items</div>
            <div class="items-grid-count" id="items-count">0 items</div>
          </div>
          <div id="items-grid" class="items-grid">

            {{-- Services --}}
            @foreach($services as $service)
              @php
                $serviceIcons = ['scissors', 'star', 'heart', 'droplet', 'sun', 'moon', 'gem'];
                $serviceIcon = $serviceIcons[$loop->index % count($serviceIcons)];
              @endphp
              <div class="item-card" data-id="{{ $service->id }}" data-name="{{ strtolower($service->name) }}"
                data-category="{{ $service->category_id }}" data-barcode="{{ $service->barcode ?? '' }}" data-type="service"
                onclick='addToCart({!! json_encode(["id" => $service->id, "name" => $service->name, "price" => (float) $service->price, "type" => "service"]) !!})'>
                @if($service->is_popular)<span class="item-badge badge-hot">Hot</span>@endif
                <div class="item-thumb svc"><i data-lucide="{{ $serviceIcon }}"></i></div>
                <div class="item-body">
                  <div class="item-name">{{ $service->name }}</div>
                  <div class="item-sub">{{ $service->duration ?? '—' }} mins</div>
                  <div class="item-footer">
                    <span class="item-price">PKR {{ number_format($service->price, 0) }}</span>
                    <button class="item-add svc"><i data-lucide="plus"></i></button>
                  </div>
                </div>
              </div>
            @endforeach

            {{-- Packages --}}
            @foreach($packages as $package)
              <div class="item-card pkg" style="display:none" data-id="{{ $package->id }}"
                data-name="{{ strtolower($package->name) }}" data-type="package"
                onclick='addToCart({!! json_encode(["id" => $package->id, "name" => $package->name, "price" => (float) $package->price, "type" => "package"]) !!})'>
                <span class="item-badge badge-bundle">Bundle</span>
                <div class="item-thumb pkg"><i data-lucide="package"></i></div>
                <div class="item-body">
                  <div class="item-name">{{ $package->name }}</div>
                  <div class="item-sub">{{ $package->services->count() }} services</div>
                  <div class="item-footer">
                    <span class="item-price">PKR {{ number_format($package->price, 0) }}</span>
                    <button class="item-add pkg"><i data-lucide="plus"></i></button>
                  </div>
                </div>
              </div>
            @endforeach

            {{-- Products --}}
            @foreach($products as $product)
              <div class="item-card prod" style="display:none" data-id="{{ $product->id }}"
                data-name="{{ strtolower($product->name) }}" data-category="{{ $product->category_id }}"
                data-barcode="{{ $product->barcode ?? '' }}" data-type="product"
                onclick='addToCart({!! json_encode(["id" => $product->id, "name" => $product->name, "price" => (float) $product->price, "type" => "product"]) !!})'>
                <div class="item-thumb prod"><i data-lucide="box"></i></div>
                <div class="item-body">
                  <div class="item-name">{{ $product->name }}</div>
                  <div class="item-sub">Stock: {{ $product->current_stock ?? '—' }}</div>
                  <div class="item-footer">
                    <span class="item-price">PKR {{ number_format($product->price, 0) }}</span>
                    <button class="item-add prod"><i data-lucide="plus"></i></button>
                  </div>
                </div>
              </div>
            @endforeach

          </div>
        </div>
      </div>

      {{-- Order Panel --}}
      <div class="order-panel">

        {{-- Scrollable content --}}
        <div class="order-scroll">

          {{-- Order Summary card --}}
          <div class="order-section">
            <div class="order-head">
              <div class="order-head-row">
                <span class="order-title">Order Summary</span>
                <button onclick="clearCart()" class="clear-btn">Clear</button>
              </div>
              <div style="font-size:11px;color:#9ca3af;margin-bottom:10px;display:flex;align-items:center;gap:6px">
                <span>Customer:</span>
                <span id="customer-display" style="font-weight:700;color:#374151">Walk-in Customer</span>
                <span class="customer-tag">Active</span>
              </div>
              <input id="walkin-name" type="text" placeholder="Customer name (optional)"
                oninput="syncCustomerName(this.value)"
                style="width:100%;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 12px;font-size:12px;font-weight:400;font-family:'Outfit',sans-serif;outline:none;color:#374151;transition:all .2s"
                onfocus="this.style.borderColor='#F7DF79';this.style.boxShadow='0 0 0 3px rgba(247,223,121,.15)'"
                onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
            </div>
            <div style="padding:6px 14px 4px">
              <div class="order-subtitle">Selected Services</div>
            </div>
            <div id="cart-container" class="cart-body">
              <div id="empty-cart" class="empty-cart">
                <i data-lucide="shopping-cart"></i>
                <p>Cart is empty</p>
              </div>
            </div>
          </div>

          {{-- Order Totals card --}}
          <div class="order-section order-foot">
            <div class="order-foot-scroll">
              <div class="order-subtitle">Order Totals</div>
              <div class="totals-lines">
                <div class="tot-row"><span>Subtotal</span><span id="label-subtotal">PKR 0.00</span></div>
                <div class="tot-row"><span>Tax (5%)</span><span id="label-tax">PKR 0.00</span></div>
              </div>
              <div class="tot-divider"></div>
              <div class="tot-total">
                <span class="tot-total-label">Estimated Total</span>
                <span id="label-total" class="tot-total-val">PKR 0.00</span>
              </div>
              <div class="customer-chip">
                <span class="customer-chip-lbl">Billing For</span>
                <span id="customer-chip-name" class="customer-chip-name">Walk-in Customer</span>
              </div>
            </div>
          </div>

        </div>{{-- end order-scroll --}}

        {{-- Pinned checkout button --}}
        <div class="order-checkout-pin">
          <button onclick="goToPayment()" id="checkout-btn" class="checkout-btn" disabled>
            PROCEED
          </button>
        </div>

      </div>
    </div>{{-- close inner flex wrapper --}}
  </div>{{-- close pos-page --}}

  {{-- Success Modal --}}
  <div id="invoice-modal" class="modal-overlay" style="display:none">
    <div class="modal-box" style="width: 400px;">
      <div class="modal-icon"><i data-lucide="check"></i></div>
      <div class="modal-title">Checkout Complete!</div>
      <div id="modal-invoice-no" class="modal-inv">#INVOICE-0000</div>
      <div id="modal-fbr-status" style="font-size:12px;margin:10px 0;font-weight:700 text-align:center"></div>

      <!-- Hidden iframe for instant on-page printing -->
      <iframe id="receipt-iframe"
        style="position: absolute; width: 0; height: 0; border: none; visibility: hidden;"></iframe>

      <div class="modal-actions" style="margin-top: 20px;">
        <button id="print-receipt" class="modal-btn print"
          onclick="document.getElementById('receipt-iframe').contentWindow.print();"><i data-lucide="printer"></i>Re-Print
          Receipt</button>
        <button id="whatsapp-share" class="modal-btn wa"><i data-lucide="message-circle"></i>WhatsApp</button>
      </div>
      <button onclick="newOrder()" class="modal-new">Done & Next Customer</button>
    </div>
  </div>
  <script>
    let cart = [], currentTab = 'all';
    let customerName = 'Walk-in Customer';
    let activeCategory = 'all';
    let emptyCartNode = null;

    document.addEventListener('DOMContentLoaded', () => {
      emptyCartNode = document.getElementById('empty-cart');
      lucide.createIcons();
      document.getElementById('item-search').addEventListener('input', e => {
        const v = e.target.value.toLowerCase();
        document.querySelectorAll('.item-card').forEach(c => {
          const match = c.dataset.name.includes(v) || (c.dataset.barcode || '').includes(v);
          const tabOk = currentTab === 'all' || c.dataset.type === currentTab.slice(0, -1);
          c.style.display = (match && tabOk) ? '' : 'none';
          if (c.dataset.barcode === v && v.length > 3) { c.click(); e.target.value = ''; }
        });
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', e => {
        if (!e.target.closest('#hcat-more-wrap')) {
          const menu = document.getElementById('hcat-more-menu');
          if (menu && menu.classList.contains('show')) menu.classList.remove('show');
        }
      });

      syncCustomerName('');
      renderItemsGrid();
      
      // Removed old organizeCategories logic
    });
    
    function toggleExpandLayout() {
      const wrap = document.querySelector('.hcat-wrap');
      const icon = document.querySelector('.hcat-more-btn i');
      wrap.classList.toggle('expanded');
      
      if(wrap.classList.contains('expanded')) {
        icon.setAttribute('data-lucide', 'chevron-up');
      } else {
        icon.setAttribute('data-lucide', 'chevron-down');
      }
      lucide.createIcons();
    }

    function getCustomerName() {
      const input = document.getElementById('walkin-name');
      const value = input ? input.value.trim() : '';
      return value || 'Walk-in Customer';
    }

    function syncCustomerName(value) {
      customerName = (value || '').trim() || 'Walk-in Customer';
      document.getElementById('customer-display').innerText = customerName;
      document.getElementById('customer-chip-name').innerText = customerName;
    }

    function addToCart(item) {
      const ex = cart.find(i => i.id === item.id && i.type === item.type);
      ex ? ex.quantity++ : cart.push({ ...item, quantity: 1 });
      renderCart();
    }
    function updateQty(idx, delta) {
      cart[idx].quantity += delta;
      if (cart[idx].quantity < 1) cart.splice(idx, 1);
      renderCart();
    }
    function manualUpdateQty(idx, val) {
      const n = parseInt(val);
      if (isNaN(n) || n < 1) {
        cart.splice(idx, 1);
      } else {
        cart[idx].quantity = n;
      }
      renderCart();
    }
    function clearCart() {
      cart = []; renderCart();
    }

    async function checkout() {
      if (cart.length === 0) return;
      const btn = document.getElementById('checkout-btn');
      btn.disabled = true; btn.innerText = 'PROCESSING...';

      const sub = cart.reduce((a, i) => a + i.price * i.quantity, 0);
      const tax = sub * 0.05; // 5% GST
      const total = sub + tax;
      const activeCustomerName = getCustomerName();

      try {
        const res = await fetch('{{ route("pos.store") }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({
            items: cart,
            payment_method: 'cash', // fallback as split logic was omitted
            total_amount: sub,
            tax: tax,
            discount: 0,
            payable_amount: total,
            tendered_amount: total,
            customer_id: null,
            customer_name: activeCustomerName
          })
        });

        if (!res.ok) throw new Error('Server returned ' + res.status);
        const data = await res.json();

        if (data.success) {
          // 1. Fill Modal
          document.getElementById('modal-invoice-no').innerText = `#${data.invoice.invoice_no}`;
          const fbrEl = document.getElementById('modal-fbr-status');
          if (fbrEl && data.fbr) {
            if (data.fbr.Code === '100') {
              fbrEl.innerHTML = `<span style="color:#16a34a">✔ FBR SYNCED: ${data.fbr.FBRInvoiceNumber}</span>`;
            } else {
              fbrEl.innerHTML = `<span style="color:#ef4444">✖ FBR ERROR: ${data.fbr.Response}</span>`;
            }
          }

          // 2. Setup embedded iframe for silent printing natively on the same page
          const frame = document.getElementById('receipt-iframe');
          frame.src = `/invoices/${data.invoice.id}`;

          document.getElementById('whatsapp-share').onclick = () => window.open(`https://wa.me/?text=${encodeURIComponent('Invoice: ' + window.location.origin + '/invoices/' + data.invoice.id)}`, '_blank');

          // 3. Show Modal
          document.getElementById('invoice-modal').style.display = 'flex';
          lucide.createIcons();
        } else {
          showAppMessage('Checkout Failed: ' + data.message, 'error');
          btn.disabled = false; btn.innerText = 'PROCEED TO CHECKOUT';
        }
      } catch (e) {
        showAppMessage('Checkout Exception: ' + e.message, 'error');
        btn.disabled = false; btn.innerText = 'PROCEED TO CHECKOUT';
      }
    }

    function newOrder() {
      document.getElementById('invoice-modal').style.display = 'none';
      clearCart();
      document.getElementById('checkout-btn').innerText = 'PROCEED TO CHECKOUT';
      const nameInput = document.getElementById('walkin-name');
      if (nameInput) { nameInput.value = ''; syncCustomerName(''); }
      // wipe iframe to stop old loads overlaying
      document.getElementById('receipt-iframe').src = 'about:blank';
    }
    function renderCart() {
      const container = document.getElementById('cart-container');
      const btn = document.getElementById('checkout-btn');
      if (!container || !btn) return;

      if (cart.length === 0) {
        container.innerHTML = '';
        if (emptyCartNode) {
          container.appendChild(emptyCartNode);
          emptyCartNode.style.display = 'flex';
        }
        btn.disabled = true;
      } else {
        if (emptyCartNode) emptyCartNode.style.display = 'none';

        container.innerHTML = cart.map((item, i) => `
          <div class="cart-item">
            <div class="cart-item-info">
              <div class="cart-item-name">${item.name}</div>
              <div class="cart-item-price">PKR ${(item.price * item.quantity).toFixed(2)}</div>
            </div>
            <div class="qty-ctrl">
              <button onclick="updateQty(${i},-1)" class="qty-btn"><i data-lucide="minus"></i></button>
              <input type="number" class="qty-num-input" value="${item.quantity}" min="1" 
                onchange="manualUpdateQty(${i}, this.value)"
                onkeyup="if(event.key==='Enter') this.blur()"
                onclick="event.stopPropagation()">
              <button onclick="updateQty(${i},1)" class="qty-btn"><i data-lucide="plus"></i></button>
            </div>
          </div>`).join('');
        btn.disabled = false;
        lucide.createIcons();
      }
      updateTotals();
    }

    function updateTotals() {
      const sub = cart.reduce((a, i) => a + i.price * i.quantity, 0);
      const tax = sub * 0.05;
      const total = sub + tax;
      document.getElementById('label-subtotal').innerText = `PKR ${sub.toFixed(2)}`;
      document.getElementById('label-tax').innerText = `PKR ${tax.toFixed(2)}`;
      document.getElementById('label-total').innerText = `PKR ${total.toFixed(2)}`;
    }

    function goToPayment() {
      if (!cart.length) return;
      const payload = {
        items: cart,
        customer_name: getCustomerName()
      };
      sessionStorage.setItem('pos_payment_payload', JSON.stringify(payload));
      window.location.href = '{{ route("pos.payment") }}';
    }

    function filterCategory(catId) {
      activeCategory = String(catId);
      document.querySelectorAll('.hcat-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.cat === activeCategory);
      });
      renderItemsGrid();
      
      // Close expansion if it was open
      const wrap = document.querySelector('.hcat-wrap');
      if (wrap.classList.contains('expanded')) {
        toggleExpandLayout();
      }
    }

    function switchTab(tab) {
      currentTab = tab;
      document.querySelectorAll('.pos-tab').forEach(b => b.classList.remove('active'));
      const activeBtn = document.getElementById('tab-' + tab);
      if(activeBtn) activeBtn.classList.add('active');
      renderItemsGrid();
    }

    // organizeCategories logic removed per request for simple downward expansion

    function renderItemsGrid() {
      const activeCat = activeCategory;
      const search = document.getElementById('item-search').value.toLowerCase();
      let visibleCount = 0;
      document.querySelectorAll('.item-card').forEach(card => {
        const catOk = activeCat === 'all' || card.dataset.category === activeCat;
        const tabOk = currentTab === 'all' || card.dataset.type === currentTab.slice(0, -1);
        const searchOk = card.dataset.name.includes(search) || (card.dataset.barcode || '').includes(search);
        const visible = catOk && tabOk && searchOk;
        card.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
      });
      document.getElementById('items-count').innerText = `${visibleCount} item${visibleCount === 1 ? '' : 's'}`;
    }
  </script>
@endsection