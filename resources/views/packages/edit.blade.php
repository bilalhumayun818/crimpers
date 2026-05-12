@extends('layouts.app')

@section('page-title', 'Edit Package')
@section('page-sub', 'Update the details of ' . $package->name)

@section('content')
<style>
.pkg-form-wrap{max-width:900px;margin:0 auto;font-family:'Outfit',sans-serif;display:flex;flex-direction:column;gap:20px}

/* Header */
.form-header{display:flex;align-items:center;justify-content:space-between}
.form-title{font-size:22px;font-weight:900;color:#111827;margin:0}
.form-sub{font-size:13px;color:#9ca3af;margin:3px 0 0}
.back-btn{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:700;color:#6b7280;text-decoration:none;padding:8px 16px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;transition:all .2s}
.back-btn:hover{border-color:#111827;color:#111827}
.back-btn svg{width:15px;height:15px}

/* Layout */
.pkg-grid{display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start}

/* Cards */
.form-card{background:#fff;border-radius:20px;border:1px solid #e5e7eb;box-shadow:0 1px 6px rgba(0,0,0,.05);overflow:hidden;margin-bottom:16px}
.form-card:last-child{margin-bottom:0}
.form-card-header{padding:18px 22px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px}
.card-icon{width:38px;height:38px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.card-icon.yellow{background:#fef9c3}
.card-icon.purple{background:#f3e8ff}
.card-icon svg{width:18px;height:18px}
.card-icon.yellow svg{color:#ca8a04}
.card-icon.purple svg{color:#9333ea}
.card-title{font-size:14px;font-weight:800;color:#111827}
.card-sub{font-size:11px;color:#9ca3af;margin-top:1px}
.form-card-body{padding:22px;display:flex;flex-direction:column;gap:16px}

/* Fields */
.field label{display:block;font-size:11px;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.field input,.field select,.field textarea{width:100%;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:12px;padding:11px 14px;font-size:14px;font-family:'Outfit',sans-serif;color:#111827;outline:none;transition:all .2s}
.field input:focus,.field select:focus,.field textarea:focus{border-color:#F7DF79;background:#fff;box-shadow:0 0 0 3px rgba(247,223,121,.2)}
.field textarea{resize:vertical;min-height:90px}
.field .error{font-size:11px;color:#ef4444;font-weight:600;margin-top:4px}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}

/* Services grid */
.services-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;max-height:360px;overflow-y:auto;padding-right:4px}
.services-grid::-webkit-scrollbar{width:4px}
.services-grid::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:4px}
.svc-check-label{display:flex;align-items:center;gap:12px;padding:12px 14px;border:1.5px solid #f1f1f1;border-radius:14px;cursor:pointer;transition:all .2s;background:#fff}
.svc-check-label:hover{border-color:#F7DF79;background:#fef9c3}
.svc-check-label input[type=checkbox]{width:16px;height:16px;accent-color:#F7DF79;flex-shrink:0;cursor:pointer}
.svc-check-label input[type=checkbox]:checked~.svc-info .svc-check-name{color:#92400e}
.svc-check-label:has(input:checked){border-color:#F7DF79;background:#fef9c3}
.svc-check-icon{width:32px;height:32px;background:#f9fafb;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.svc-check-icon svg{width:15px;height:15px;color:#9ca3af}
.svc-check-label:has(input:checked) .svc-check-icon{background:#F7DF79}
.svc-check-label:has(input:checked) .svc-check-icon svg{color:#111827}
.svc-info{flex:1;min-width:0}
.svc-check-name{font-size:12px;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.svc-check-meta{font-size:10px;color:#9ca3af;margin-top:1px}

/* Sidebar */
.sidebar-card{background:#fff;border-radius:20px;border:1px solid #e5e7eb;box-shadow:0 1px 6px rgba(0,0,0,.05);overflow:hidden;position:sticky;top:20px}
.sidebar-body{padding:20px;display:flex;flex-direction:column;gap:14px}
.sidebar-title{font-size:11px;font-weight:800;color:#9ca3af;text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px}

/* Toggle */
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:#f9fafb;border-radius:12px;border:1.5px solid #e5e7eb}
.toggle-row-label{font-size:13px;font-weight:700;color:#374151}
.toggle{position:relative;width:42px;height:22px;flex-shrink:0}
.toggle input{opacity:0;width:0;height:0;position:absolute}
.toggle-track{position:absolute;inset:0;background:#e5e7eb;border-radius:22px;transition:background .2s;cursor:pointer}
.toggle input:checked~.toggle-track{background:#F7DF79}
.toggle-thumb{position:absolute;top:3px;left:3px;width:16px;height:16px;background:#fff;border-radius:50%;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .2s;pointer-events:none}
.toggle input:checked~.toggle-thumb{transform:translateX(20px)}

/* Summary box */
.summary-box{background:#f9fafb;border-radius:12px;border:1.5px solid #e5e7eb;padding:14px;display:flex;flex-direction:column;gap:8px}
.summary-row{display:flex;justify-content:space-between;font-size:12px;color:#6b7280}
.summary-row span:last-child{font-weight:700;color:#111827}
.summary-divider{height:1px;background:#e5e7eb}
.summary-total{display:flex;justify-content:space-between;font-size:14px;font-weight:900;color:#111827}

/* Buttons */
.btn-submit{width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;border-radius:12px;border:none;background:#F7DF79;font-size:13px;font-weight:900;color:#111827;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s;box-shadow:0 4px 12px rgba(247,223,121,.4)}
.btn-submit:hover{background:#fde047;transform:translateY(-1px)}
.btn-submit svg{width:16px;height:16px}
.btn-discard{width:100%;padding:10px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;font-size:13px;font-weight:700;color:#6b7280;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s;text-align:center;text-decoration:none;display:block}
.btn-discard:hover{border-color:#9ca3af;color:#111827}
.sidebar-note{font-size:11px;color:#9ca3af;text-align:center;line-height:1.5}

/* Delete zone */
.delete-zone{background:#fff;border-radius:20px;border:1.5px dashed #fecaca;box-shadow:0 1px 6px rgba(0,0,0,.05);overflow:hidden;margin-top:16px}
.delete-zone-body{padding:20px;display:flex;flex-direction:column;gap:10px}
.delete-zone-title{font-size:13px;font-weight:800;color:#dc2626}
.delete-zone-desc{font-size:11px;color:#9ca3af;line-height:1.5}
.btn-delete{width:100%;padding:10px;border-radius:10px;border:1.5px solid #fecaca;background:#fef2f2;font-size:12px;font-weight:700;color:#dc2626;cursor:pointer;font-family:'Outfit',sans-serif;transition:all .2s}
.btn-delete:hover{background:#dc2626;color:#fff;border-color:#dc2626}
</style>

<div class="pkg-form-wrap">

    {{-- Header --}}
    <div class="form-header">
        <div>
            <h1 class="form-title">Edit Package</h1>
            <p class="form-sub">Updating details for <strong>{{ $package->name }}</strong></p>
        </div>
        <a href="{{ route('packages.index') }}" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            Back
        </a>
    </div>

    {{-- Errors --}}
    @if($errors->any())
    <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:12px;padding:14px 18px">
        <p style="font-size:12px;font-weight:800;color:#dc2626;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em">Please fix the following:</p>
        @foreach($errors->all() as $error)
        <p style="font-size:13px;color:#ef4444;font-weight:600">• {{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('packages.update', $package) }}" method="POST" id="pkg-form">
        @csrf
        @method('PUT')

        <div class="pkg-grid">

            {{-- Left Column --}}
            <div>

                {{-- Package Info --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="card-icon yellow">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                        </div>
                        <div>
                            <div class="card-title">Package Information</div>
                            <div class="card-sub">Name, description, price and duration</div>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field">
                            <label>Package Name</label>
                            <input type="text" name="name" placeholder="e.g. Bridal Glow Bundle" required value="{{ old('name', $package->name) }}">
                            @error('name')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field">
                            <label>Description <span style="font-size:10px;color:#9ca3af;text-transform:none;letter-spacing:0">(optional)</span></label>
                            <textarea name="description" placeholder="Describe what's included in this package…">{{ old('description', $package->description) }}</textarea>
                            @error('description')<p class="error">{{ $message }}</p>@enderror
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label>Price (PKR)</label>
                                <input type="number" name="price" id="pkg-price" placeholder="0" required step="0.01" min="0" value="{{ old('price', $package->price) }}" oninput="updateSummary()">
                                @error('price')<p class="error">{{ $message }}</p>@enderror
                            </div>
                            <div class="field">
                                <label>Duration (Minutes)</label>
                                <input type="number" name="duration" id="pkg-duration" placeholder="60" required min="1" value="{{ old('duration', $package->duration) }}" oninput="updateSummary()">
                                @error('duration')<p class="error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Services --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="card-icon purple">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m6 3 6 6 6-6"/><path d="M20 21H4"/><path d="m6 21 6-6 6 6"/></svg>
                        </div>
                        <div>
                            <div class="card-title">Included Services</div>
                            <div class="card-sub">Select services to bundle in this package</div>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="services-grid">
                            @php $selectedIds = old('service_ids', $package->services->pluck('id')->toArray()); @endphp
                            @foreach($services as $service)
                            <label class="svc-check-label">
                                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                                    {{ in_array($service->id, $selectedIds) ? 'checked' : '' }}
                                    onchange="updateSummary()">
                                <div class="svc-check-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 3 6 6 6-6"/><path d="M20 21H4"/><path d="m6 21 6-6 6 6"/></svg>
                                </div>
                                <div class="svc-info">
                                    <div class="svc-check-name">{{ $service->name }}</div>
                                    <div class="svc-check-meta">PKR {{ number_format($service->price, 0) }} · {{ $service->duration }}m</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('service_ids')<p class="error" style="margin-top:8px">{{ $message }}</p>@enderror
                    </div>
                </div>

            </div>

            {{-- Right Sidebar --}}
            <div>
                <div class="sidebar-card">
                    <div class="sidebar-body">

                        <div class="sidebar-title">Package Summary</div>

                        <div class="summary-box">
                            <div class="summary-row"><span>Services selected</span><span id="sum-count">0</span></div>
                            <div class="summary-row"><span>Duration</span><span id="sum-duration">— min</span></div>
                            <div class="summary-divider"></div>
                            <div class="summary-total"><span>Price</span><span id="sum-price">PKR 0</span></div>
                        </div>

                        <div class="sidebar-title" style="margin-top:4px">Status</div>

                        <div class="toggle-row">
                            <span class="toggle-row-label">Set as Active</span>
                            <label class="toggle">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                <div class="toggle-track"></div>
                                <div class="toggle-thumb"></div>
                            </label>
                        </div>

                        <button type="submit" class="btn-submit">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                            Update Package
                        </button>

                        <a href="{{ route('packages.index') }}" class="btn-discard">Discard Changes</a>

                        <p class="sidebar-note">Changes will be reflected in POS immediately after saving.</p>

                    </div>
                </div>

                {{-- Delete Zone --}}
                <div class="delete-zone">
                    <div class="delete-zone-body">
                        <div class="delete-zone-title">Delete Package</div>
                        <div class="delete-zone-desc">This will remove the package bundle but keep individual services intact.</div>
                        <button type="button" onclick="confirmDelete()" class="btn-delete">Remove Bundle</button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- Hidden Delete Form --}}
<form id="delete-package-form" action="{{ route('packages.destroy', $package) }}" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete() {
    if (confirm('Delete this package permanently?')) {
        document.getElementById('delete-package-form').submit();
    }
}
function updateSummary(){
    const checked=document.querySelectorAll('input[name="service_ids[]"]:checked');
    document.getElementById('sum-count').innerText=checked.length;
    const price=parseFloat(document.getElementById('pkg-price').value)||0;
    const dur=parseFloat(document.getElementById('pkg-duration').value)||0;
    document.getElementById('sum-price').innerText=`PKR ${price.toLocaleString()}`;
    document.getElementById('sum-duration').innerText=dur?`${dur} min`:'— min';
}
document.addEventListener('DOMContentLoaded',updateSummary);
</script>
@endsection
