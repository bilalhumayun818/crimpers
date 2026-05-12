@extends('layouts.app')

@section('content')
<style>
.pg-wrap{font-family:'Outfit',sans-serif;display:flex;flex-direction:column;gap:20px}
/* Header */
.pg-header{display:flex;align-items:center;justify-content:space-between}
.pg-title{font-size:22px;font-weight:900;color:#111827;margin:0}
.pg-sub{font-size:13px;color:#9ca3af;margin:2px 0 0}
.btn-gold{display:inline-flex;align-items:center;gap:8px;background:#F7DF79;border:none;border-radius:14px;padding:10px 20px;font-size:13px;font-weight:700;color:#111827;cursor:pointer;text-decoration:none;box-shadow:0 4px 12px rgba(247,223,121,.4);transition:all .2s}
.btn-gold:hover{background:#fde047;transform:translateY(-1px);box-shadow:0 6px 16px rgba(247,223,121,.5)}
.btn-gold svg{width:16px;height:16px}
/* Filter Bar */
.filter-bar{background:#fff;border-radius:16px;border:1px solid #f1f1f1;box-shadow:0 1px 4px rgba(0,0,0,.05);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.filter-pills{display:flex;gap:8px;flex-wrap:wrap}
.pill{padding:6px 16px;border-radius:20px;border:none;font-size:12px;font-weight:700;cursor:pointer;transition:all .2s;background:#f3f4f6;color:#6b7280;display:inline-flex;align-items:center;gap:6px}
.pill:hover{background:#fef9c3;color:#111827}
.pill.active{background:#F7DF79;color:#111827;box-shadow:0 2px 8px rgba(247,223,121,.4)}
/* Floating Bar & Modals */
.cat-edit-bar { display:none; position:fixed; bottom:30px; left:50%; transform:translateX(-50%); background:#111827; padding:12px 24px; border-radius:100px; box-shadow:0 10px 40px rgba(0,0,0,0.25); z-index:100; align-items:center; gap:16px; animation:slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes slideUp { from{ transform:translate(-50%, 40px); opacity:0; } to{ transform:translate(-50%, 0); opacity:1; } }
.cat-edit-bar span { font-size:14px; font-weight:700; color:#f9fafb; font-family:'Outfit',sans-serif; margin-right:8px; }
.cat-ebtn { background:rgba(255,255,255,0.1); border:none; padding:8px 16px; border-radius:30px; color:#fff; font-size:12px; font-weight:700; cursor:pointer; transition:all 0.2s; font-family:'Outfit',sans-serif; }
.cat-ebtn:hover { background:rgba(255,255,255,0.2); }
.cat-ebtn.del:hover { background:#ef4444; color:#fff; }
.cat-ebtn.edit:hover { background:#3b82f6; color:#fff; }
.search-box{position:relative;width:220px}
.search-box input{width:100%;background:#f3f4f6;border:none;border-radius:12px;padding:8px 14px 8px 36px;font-size:13px;font-family:'Outfit',sans-serif;outline:none;transition:box-shadow .2s}
.search-box input:focus{box-shadow:0 0 0 2px rgba(247,223,121,.6)}
.search-box svg{position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af;pointer-events:none}
/* Grid */
.svc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
/* Card */
.svc-card{background:#fff;border-radius:20px;border:1.5px solid #f1f1f1;box-shadow:0 1px 6px rgba(0,0,0,.05);overflow:hidden;transition:all .2s}
.svc-card:hover{border-color:#F7DF79;box-shadow:0 8px 24px rgba(247,223,121,.25);transform:translateY(-2px)}
.svc-card-accent{height:4px;background:linear-gradient(90deg,#F7DF79,#fde047)}
.svc-card-body{padding:18px}
.svc-card-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px}
.svc-icon{width:46px;height:46px;background:#fef9c3;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.svc-icon svg{width:22px;height:22px;color:#ca8a04}
.svc-actions{display:flex;gap:6px}
.act-btn{width:30px;height:30px;border-radius:10px;border:none;background:#f3f4f6;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s}
.act-btn svg{width:13px;height:13px;color:#9ca3af}
.act-btn.edit:hover{background:#dbeafe}
.act-btn.edit:hover svg{color:#3b82f6}
.act-btn.del:hover{background:#fee2e2}
.act-btn.del:hover svg{color:#ef4444}
.svc-badge{display:inline-block;padding:3px 10px;background:#f3f4f6;border-radius:20px;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px}
.svc-name{font-size:15px;font-weight:900;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px}
.svc-dur{display:flex;align-items:center;gap:4px;font-size:11px;color:#9ca3af;margin-bottom:14px}
.svc-dur svg{width:12px;height:12px}
.svc-footer{display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid #f3f4f6}
.svc-price-label{font-size:9px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.08em;margin-bottom:2px}
.svc-price{font-size:20px;font-weight:900;color:#111827}
.popular-badge{display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#92400e;padding:4px 10px;border-radius:10px;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.05em}
.popular-badge svg{width:11px;height:11px;fill:#ca8a04;color:#ca8a04}
/* Empty */
.empty-state{grid-column:1/-1;background:#fff;border-radius:20px;border:2px dashed #e5e7eb;padding:60px 20px;display:flex;flex-direction:column;align-items:center;gap:14px}
.empty-icon{width:60px;height:60px;background:#fef9c3;border-radius:50%;display:flex;align-items:center;justify-content:center}
.empty-icon svg{width:28px;height:28px;color:#ca8a04}
.empty-text{font-size:14px;color:#9ca3af;font-weight:500}
</style>

<div class="pg-wrap">
    {{-- Header --}}
    <div class="pg-header">
        <div>
            <h2 class="pg-title">Salon Services</h2>
            <p class="pg-sub">Manage your professional services and pricing.</p>
        </div>
        {{-- <a href="{{ route('services.create') }}" class="btn-gold">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Add Service
        </a> --}}
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <div class="filter-pills">
            <button onclick="filterServices('all', null)" id="pill-all" class="pill active">All Services</button>
            @foreach($categories as $category)
            <button onclick="filterServices('{{ $category->id }}', '{{ addslashes($category->name) }}')" id="pill-{{ $category->id }}" class="pill">{{ $category->name }}</button>
            @endforeach
        </div>
        <div class="search-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" id="svc-search" placeholder="Search services…" oninput="filterServices(currentCategory)">
        </div>
    </div>

    {{-- Grid --}}
    <div class="svc-grid">
        @forelse($services as $service)
        <div class="svc-card" data-category="{{ $service->category_id }}" data-name="{{ strtolower($service->name) }}">
            <div class="svc-card-accent"></div>
            <div class="svc-card-body">
                <div class="svc-card-top">
                    <div class="svc-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                    </div>
                    <div class="svc-actions">
                        <a href="{{ route('services.edit', $service) }}" class="act-btn edit">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        </a>
                        <button type="button" onclick="confirmItemDeletion('{{ route('services.destroy', $service) }}')" class="act-btn del">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </div>
                </div>
                <span class="svc-badge">{{ $service->category->name ?? 'General' }}</span>
                <div class="svc-name">{{ $service->name }}</div>
                <div class="svc-dur">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ $service->duration ?? '—' }} mins
                </div>
                <div class="svc-footer">
                    <div>
                        <div class="svc-price-label">Price</div>
                        <div class="svc-price">PKR {{ number_format($service->price, 0) }}</div>
                    </div>
                    @if($service->is_popular)
                    <span class="popular-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Popular
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m6 3 6 6 6-6"/><path d="M20 21H4"/><path d="m6 21 6-6 6 6"/></svg>
            </div>
            <p class="empty-text">No services yet. Add your first service to get started.</p>
            <a href="{{ route('services.create') }}" class="btn-gold">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Add Service
            </a>
        </div>
        @endforelse
    </div>

    <div>{{ $services->links('pagination::tailwind') }}</div>
</div>

{{-- Floating Action Bar --}}
<div class="cat-edit-bar" id="cat-edit-bar">
   <span id="cat-edit-label">Category Name</span>
   <button onclick="promptEditCategory()" class="cat-ebtn edit">Edit Name</button>
   <button onclick="promptDeleteCategory()" class="cat-ebtn del">Delete</button>
</div>

{{-- Custom Modals --}}
<div id="cat-modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(6px);z-index:200;align-items:center;justify-content:center;font-family:'Outfit',sans-serif">

    {{-- Edit Category Modal --}}
    <div id="cat-edit-modal" style="display:none;background:#fff;border-radius:24px;width:100%;max-width:380px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.2);margin:20px">
        <div style="height:4px;background:linear-gradient(90deg,#F7DF79,#fde047)"></div>
        <div style="padding:28px">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
                <div style="width:40px;height:40px;background:#fef9c3;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                </div>
                <div>
                    <div style="font-size:16px;font-weight:900;color:#111827">Edit Category</div>
                    <div style="font-size:11px;color:#9ca3af;font-weight:400;margin-top:1px">Update the category name</div>
                </div>
            </div>
            <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Category Name</label>
            <input type="text" id="cat-edit-input" style="width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:14px;font-weight:400;outline:none;background:#f9fafb;color:#111827;transition:all .2s;margin-bottom:20px" onfocus="this.style.borderColor='#F7DF79';this.style.boxShadow='0 0 0 3px rgba(247,223,121,.2)';this.style.background='#fff'" onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none';this.style.background='#f9fafb'">
            <div style="display:flex;gap:10px">
                <button onclick="closeCatModal()" style="flex:1;padding:11px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;font-weight:700;font-size:13px;font-family:'Outfit',sans-serif;color:#374151;transition:all .2s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">Cancel</button>
                <button id="cat-save-btn" onclick="submitEditCategory()" style="flex:1;padding:11px;border-radius:12px;border:none;background:#F7DF79;color:#111827;cursor:pointer;font-weight:800;font-size:13px;font-family:'Outfit',sans-serif;transition:all .2s;box-shadow:0 4px 12px rgba(247,223,121,.4)" onmouseover="this.style.background='#fde047'" onmouseout="this.style.background='#F7DF79'">Save Changes</button>
            </div>
        </div>
    </div>

    {{-- Delete Category Modal --}}
    <div id="cat-del-modal" style="display:none;background:#fff;border-radius:24px;width:100%;max-width:380px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.2);margin:20px">
        <div style="height:4px;background:linear-gradient(90deg,#ef4444,#f87171)"></div>
        <div style="padding:32px;text-align:center">
            <div style="width:60px;height:60px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </div>
            <h3 style="font-size:20px;font-weight:900;color:#111827;margin:0 0 8px">Delete Category?</h3>
            <p style="font-size:13px;color:#9ca3af;font-weight:400;line-height:1.6;margin:0 0 28px">This will permanently delete this category. Services in this category will become uncategorized.</p>
            <div style="display:flex;gap:10px">
                <button onclick="closeCatModal()" style="flex:1;padding:12px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;font-weight:700;font-size:13px;font-family:'Outfit',sans-serif;color:#374151;transition:all .2s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">Cancel</button>
                <button id="cat-del-btn" onclick="submitDeleteCategory()" style="flex:1;padding:12px;border-radius:12px;border:none;background:#ef4444;color:#fff;cursor:pointer;font-weight:700;font-size:13px;font-family:'Outfit',sans-serif;transition:all .2s;box-shadow:0 4px 12px rgba(239,68,68,.3)" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">Delete Category</button>
            </div>
        </div>
    </div>
</div>

{{-- Global Delete Modal --}}
<div id="global-delete-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;font-family:'Outfit',sans-serif">
    <div style="background:#fff;border-radius:24px;width:100%;max-width:380px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.2);margin:20px">
        <div style="height:4px;background:linear-gradient(90deg,#ef4444,#f87171)"></div>
        <div style="padding:32px;text-align:center">
            <div style="width:60px;height:60px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </div>
            <h3 style="font-size:20px;font-weight:900;color:#111827;margin:0 0 8px">Delete Service?</h3>
            <p style="font-size:13px;color:#9ca3af;font-weight:400;line-height:1.6;margin:0 0 28px">This will permanently remove this service from your catalog and POS. This action cannot be undone.</p>
            <form id="global-delete-form" method="POST" style="display:flex;gap:10px;margin:0">
                @csrf @method('DELETE')
                <button type="button" onclick="closeGlobalDelete()" style="flex:1;padding:12px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;font-weight:700;font-size:13px;font-family:'Outfit',sans-serif;color:#374151;transition:all .2s" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">Cancel</button>
                <button type="submit" style="flex:1;padding:12px;border-radius:12px;border:none;background:#ef4444;color:#fff;cursor:pointer;font-weight:700;font-size:13px;font-family:'Outfit',sans-serif;transition:all .2s;box-shadow:0 4px 12px rgba(239,68,68,.3)" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmItemDeletion(url) {
        document.getElementById('global-delete-form').action = url;
        document.getElementById('global-delete-overlay').style.display = 'flex';
    }
    function closeGlobalDelete() {
        document.getElementById('global-delete-overlay').style.display = 'none';
        document.getElementById('global-delete-form').action = '';
    }

    let currentCategory = 'all';
    let selectedCatName = '';

    function filterServices(catId, catName) {
        currentCategory = catId;
        const search = document.getElementById('svc-search').value.toLowerCase();
        
        // Update Pill UI
        document.querySelectorAll('.pill').forEach(btn => btn.classList.remove('active'));
        if (catId === 'all') {
            document.getElementById('pill-all').classList.add('active');
            document.getElementById('cat-edit-bar').style.display = 'none';
        } else {
            document.getElementById('pill-' + catId).classList.add('active');
            selectedCatName = catName;
            document.getElementById('cat-edit-label').innerText = catName;
            document.getElementById('cat-edit-bar').style.display = 'flex';
        }

        // Filter Cards
        document.querySelectorAll('.svc-card').forEach(card => {
            const matchesCat = (catId === 'all' || card.dataset.category === catId);
            const matchesSearch = card.dataset.name.includes(search);
            card.style.display = (matchesCat && matchesSearch) ? '' : 'none';
        });
    }

    function closeCatModal() {
        document.getElementById('cat-modal-overlay').style.display = 'none';
        document.getElementById('cat-edit-modal').style.display = 'none';
        document.getElementById('cat-del-modal').style.display = 'none';
        document.getElementById('cat-edit-input').value = '';
    }

    function promptEditCategory() {
        document.getElementById('cat-edit-input').value = selectedCatName;
        document.getElementById('cat-modal-overlay').style.display = 'flex';
        document.getElementById('cat-edit-modal').style.display = 'block';
        setTimeout(() => document.getElementById('cat-edit-input').focus(), 100);
    }

    function promptDeleteCategory() {
        document.getElementById('cat-modal-overlay').style.display = 'flex';
        document.getElementById('cat-del-modal').style.display = 'block';
    }

    async function submitEditCategory() {
        const newName = document.getElementById('cat-edit-input').value.trim();
        if (!newName || newName === selectedCatName) return closeCatModal();
        
        const btn = document.getElementById('cat-save-btn');
        const og = btn.innerText; btn.innerText = 'Saving...'; btn.disabled = true;

        try {
            const res = await fetch(`/categories/${currentCategory}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ 
                    name: newName,
                    _method: 'PUT'
                })
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Update failed');
            }
        } catch (e) {
            alert('An error occurred while updating category.');
        } finally {
            btn.innerText = og; btn.disabled = false;
        }
    }

    async function submitDeleteCategory() {
        const btn = document.getElementById('cat-del-btn');
        const og = btn.innerText; btn.innerText = 'Deleting...'; btn.disabled = true;

        try {
            const res = await fetch(`/categories/${currentCategory}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Delete failed. Category might be in use.');
                closeCatModal();
            }
        } catch (e) {
            alert('An error occurred while deleting category.');
            closeCatModal();
        } finally {
             btn.innerText = og; btn.disabled = false;
        }
    }
</script>
@endsection
