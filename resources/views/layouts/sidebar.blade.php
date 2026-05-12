<style>
/* ── Sidebar Shell ── */
.sb{width:260px;min-width:260px;height:100vh;background:#f1f5f9;display:flex;flex-direction:column;overflow:hidden;flex-shrink:0}

/* ── Logo ── */
.sb-logo{padding:22px 20px 18px;display:flex;align-items:center;gap:12px;border-bottom:1px solid #f1f5f9}
.sb-logo-mark{width:40px;height:40px;background:#F7DF79;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(247,223,121,.3)}
.sb-logo-mark i{stroke:white!important;color:white!important;stroke-width:2.5}
.sb-brand{font-size:17px;font-weight:900;color:#111827;letter-spacing:-.3px;line-height:1}
.sb-brand-sub{font-size:10px;color:#9ca3af;font-weight:500;margin-top:2px}

/* ── Nav ── */
.sb-nav{flex:1;padding:14px 12px;overflow-y:auto;display:flex;flex-direction:column;gap:8px}
.sb-nav::-webkit-scrollbar{width:0}

/* Section label */
.sb-section{font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.14em;padding:14px 10px 6px;margin-top:4px}
.sb-section:first-child{padding-top:4px;margin-top:0}

/* Nav item */
.sb-item{display:flex;align-items:center;background:#E9EDF1;gap:16px;padding:11px 16px;border-radius:9999px;text-decoration:none;color:#6b7280;font-size:13px;font-weight:600;transition:all .18s;position:relative;border:1px solid transparent}
.sb-item i{width:18px;height:18px;flex-shrink:0;opacity:.7;transition:opacity .18s}
.sb-item:hover{background:#eef0f3;color:#111827;border-color:#e2e5e9}
.sb-item:hover i{opacity:1}

/* Active */
.sb-item.active{background:#F7DF79;color:#111827;border-color:transparent;box-shadow:0 4px 16px rgba(247,223,121,.35)}
.sb-item.active i{opacity:1;color:#111827}
.sb-item.active:hover{background:#fde047}

/* Active left bar accent */
.sb-item.active::before{content:'';position:absolute;left:-12px;top:50%;transform:translateY(-50%);width:3px;height:60%;background:#F7DF79;border-radius:0 3px 3px 0}

/* ── Footer ── */
.sb-footer{padding:14px 12px;border-top:1px solid #e5e7eb}
.sb-user{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:#eef0f3;border:1px solid #e2e5e9}
.sb-avatar{width:34px;height:34px;border-radius:10px;object-fit:cover;flex-shrink:0;border:2px solid #F7DF79}
.sb-user-name{font-size:13px;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.2}
.sb-user-role{font-size:10px;color:#9ca3af;font-weight:500;margin-top:2px}
.sb-logout{margin-left:auto;flex-shrink:0;background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;border-radius:6px;transition:all .2s;display:flex;align-items:center}
.sb-logout:hover{color:#ef4444;background:rgba(239,68,68,.1)}
.sb-logout i{width:15px;height:15px}
</style>

<aside class="sb">

    {{-- Logo --}}
    <div class="sb-logo">
        <div class="sb-logo-mark">
            <i data-lucide="crown" style="width:24px;height:24px"></i>
        </div>
        <div>
            <div class="sb-brand">The Crimpers</div>
            <div class="sb-brand-sub">Salon Management</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sb-nav">

        <div class="sb-section">Main</div>

        <a href="{{ route('pos.index') }}" class="sb-item {{ request()->routeIs('pos.index') ? 'active' : '' }}">
            <i data-lucide="shopping-cart"></i>
            <span>POS Terminal</span>
        </a>

        <div class="sb-section">Catalog</div>

        <a href="{{ route('services.index') }}" class="sb-item {{ request()->routeIs('services.index') ? 'active' : '' }}">
            <i data-lucide="sparkles"></i>
            <span>Services</span>
        </a>
        <a href="{{ route('services.create') }}" class="sb-item {{ request()->routeIs('services.create') ? 'active' : '' }}">
            <i data-lucide="plus-circle"></i>
            <span>Add Service</span>
        </a>

        <a href="{{ route('packages.index') }}" class="sb-item {{ request()->routeIs('packages.index') ? 'active' : '' }}">
            <i data-lucide="package"></i>
            <span>Packages</span>
        </a>
        <a href="{{ route('packages.create') }}" class="sb-item {{ request()->routeIs('packages.create') ? 'active' : '' }}">
            <i data-lucide="plus-circle"></i>
            <span>Create Package</span>
        </a>

        <div class="sb-section">Business</div>

        <a href="{{ route('invoices.index') }}" class="sb-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i data-lucide="trending-up"></i>
            <span>Sale History</span>
        </a>

        <a href="{{ route('fbr.index') }}" class="sb-item {{ request()->routeIs('fbr.index') ? 'active' : '' }}">
            <i data-lucide="shield-check"></i>
            <span>FBR Integration</span>
        </a>

    </nav>

    {{-- User Footer --}}
    <div class="sb-footer">
        <div class="sb-user">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=F7DF79&color=111827&bold=true&size=64" class="sb-avatar" alt="">
            <div style="flex:1;min-width:0">
                <div class="sb-user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="sb-user-role">{{ ucfirst(Auth::user()->role ?? 'Administrator') }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display:contents">
                @csrf
                <button type="submit" class="sb-logout" title="Logout">
                    <i data-lucide="log-out"></i>
                </button>
            </form>
        </div>
    </div>

</aside>
