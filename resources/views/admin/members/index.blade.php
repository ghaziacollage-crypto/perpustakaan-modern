@extends('layouts.app')

@section('title', 'Data Anggota')
@section('page-title', 'Data Anggota')

@section('breadcrumb')
<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 pt-1">
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">Manajemen</li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-300 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-gray-900">Data Anggota</li>
</ul>
@endsection

@push('custom-css')
<style>
/── Card ──/
.card {
    border: 3px solid var(--comic-dark) !important;
    box-shadow: 6px 6px 0 var(--comic-dark) !important;
    border-radius: 0 !important;
}
.card .card-header {
    background: var(--comic-dark) !important;
    border-bottom: 3px solid var(--comic-orange) !important;
    padding: 14px 20px;
}
.card .card-header .card-title {
    font-family: 'Bangers', cursive !important;
    color: var(--comic-orange) !important;
    font-size: 1.2rem !important;
    letter-spacing: 3px !important;
    margin: 0;
}

/── Search Bar ──/
.comic-search-bar {
    background: var(--comic-dark);
    border: 3px solid var(--comic-dark);
    box-shadow: 6px 6px 0 var(--comic-orange);
    padding: 20px 24px;
    margin-bottom: 20px;
    position: relative;
}
.comic-search-bar .form-label {
    font-family: 'Fredoka One', cursive;
    font-size: 0.7rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--comic-orange);
    margin-bottom: 4px;
    display: block;
    font-weight: 900;
}
.comic-search-bar .form-control,
.comic-search-bar .form-select {
    border: 2px solid var(--comic-dark) !important;
    border-radius: 0 !important;
    box-shadow: 3px 3px 0 var(--comic-dark) !important;
    font-family: 'Fredoka One', cursive;
    font-weight: 800;
    color: var(--comic-dark) !important;
    background: #fff !important;
}
.comic-search-bar .form-control:focus {
    border-color: var(--comic-orange) !important;
    box-shadow: 4px 4px 0 var(--comic-orange) !important;
}
.comic-search-bar .form-control::placeholder {
    color: #aaa !important;
    font-weight: 700 !important;
}
.comic-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--comic-orange) !important;
    font-size: 1.2rem;
    z-index: 5;
    pointer-events: none;
}
.comic-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.comic-search-wrap .form-control {
    padding-left: 40px !important;
}

/── Table ──/
.comic-table-wrap {
    overflow-x: auto;
}
.comic-table-wrap table thead tr th {
    background: var(--comic-cream) !important;
    border-bottom: 3px solid var(--comic-dark) !important;
    font-family: 'Fredoka One', cursive !important;
    font-size: 0.68rem !important;
    letter-spacing: 2px !important;
    text-transform: uppercase;
    color: var(--comic-dark) !important;
    padding: 12px 16px !important;
}
.comic-table-wrap table tbody tr:hover td {
    background: rgba(255,107,53,0.06) !important;
}
.comic-table-wrap table tbody tr td {
    border-bottom: 1px solid rgba(26,26,46,0.08) !important;
    padding: 10px 16px !important;
    vertical-align: middle;
}

/── Toolbar Buttons ──/
.btn-toolbar {
    background: var(--comic-yellow);
    color: var(--comic-dark);
    border: 2px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    font-family: 'Fredoka One', cursive;
    font-size: 0.75rem;
    border-radius: 0;
    font-weight: 900;
    letter-spacing: 1px;
    padding: 8px 16px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-toolbar:hover {
    background: var(--comic-orange);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--comic-dark);
}
.btn-toolbar-export {
    background: var(--comic-blue);
    color: #fff;
    border: 2px solid var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
    font-family: 'Fredoka One', cursive;
    font-size: 0.75rem;
    border-radius: 0;
    font-weight: 900;
    letter-spacing: 1px;
    padding: 8px 16px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-toolbar-export:hover {
    background: var(--comic-yellow);
    color: var(--comic-dark);
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--comic-dark);
}

/── Action Buttons ──/
.action-group {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: flex-end;
}
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'Fredoka One', cursive;
    font-size: 0.7rem;
    font-weight: 900;
    letter-spacing: 1px;
    padding: 6px 14px;
    border-radius: 0;
    border: 2.5px solid;
    transition: all 0.2s ease;
    text-decoration: none;
    white-space: nowrap;
}
.action-btn-detail {
    background: var(--comic-cream);
    color: var(--comic-blue);
    border-color: var(--comic-blue);
    box-shadow: 3px 3px 0 var(--comic-blue);
}
.action-btn-detail:hover {
    background: var(--comic-blue);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--comic-dark);
}
.action-btn-edit {
    background: var(--comic-yellow);
    color: var(--comic-dark);
    border-color: var(--comic-dark);
    box-shadow: 3px 3px 0 var(--comic-dark);
}
.action-btn-edit:hover {
    background: var(--comic-orange);
    color: #fff;
    border-color: var(--comic-orange);
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--comic-dark);
}
.action-btn-delete {
    background: var(--comic-cream);
    color: var(--comic-red);
    border-color: var(--comic-red);
    box-shadow: 3px 3px 0 var(--comic-red);
}
.action-btn-delete:hover {
    background: var(--comic-red);
    color: #fff;
    border-color: var(--comic-red);
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 var(--comic-dark);
}

/── Empty State ──/
.comic-empty {
    text-align: center;
    padding: 40px 20px;
}
.empty-emoji {
    display: block;
    font-size: 3rem;
    margin-bottom: 8px;
}
.empty-title {
    font-family: 'Bangers', cursive;
    font-size: 1.2rem;
    color: var(--comic-dark);
    letter-spacing: 2px;
}
.empty-sub {
    font-size: 0.82rem;
    color: #888;
    margin-top: 4px;
}
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="fw-bold text-white" style="font-family:'Bangers',cursive; letter-spacing:2px; font-size:1.1rem;">👥 DAFTAR ANGGOTA</span>
        </div>
        <div class="card-toolbar d-flex align-items-center gap-2">
            <form action="{{ route('admin.members.bulk-qr-regenerate') }}" method="POST"
                  onsubmit="return confirm('Sinkronkan ulang semua QR Code anggota?\nIni akan meregenerasi QR untuk semua member.');">
                @csrf
                <button type="submit" class="btn-toolbar-export" style="background: #27ae60; color: #fff;">
                    <i class="ki-duotone ki-arrows-circle fs-2"></i> Sinkronisasi QR
                </button>
            </form>
            <a href="{{ route('admin.export.members') }}" class="btn-toolbar-export">
                <i class="ki-duotone ki-tablet-ks fs-2"></i> Export
            </a>
            <button type="button" class="btn-toolbar" data-bs-toggle="modal" data-bs-target="#modal-add-member">
                <i class="ki-duotone ki-plus fs-2"></i> Tambah
            </button>
        </div>
    </div>

    <div class="card-body py-4 px-4">

        {{-- Status Filter Tabs --}}
        <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
            @php
                $tabs = [
                    '' => ['label' => 'Semua', 'icon' => '👥'],
                    'active' => ['label' => 'Aktif', 'icon' => '🟢'],
                    'pending' => ['label' => '⏳ Pending', 'icon' => '⏳'],
                    'inactive' => ['label' => 'Nonaktif', 'icon' => '🔴'],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ route('admin.members.index', ['status' => $key] + (request('search') ? ['search' => request('search')] : [])) }}"
                   class="btn btn-sm fw-bold {{ (request('status') == $key && !is_numeric(request('status'))) || (request('status') === null && $key === '') ? '' : '' }}"
                   style="border-radius: 0; border: 3px solid var(--comic-dark); font-family: 'Fredoka One', cursive; font-size: 0.8rem; letter-spacing: 1px; padding: 8px 16px; {{ (request('status') == $key) ? 'background: var(--comic-dark); color: var(--comic-orange);' : 'background: var(--comic-cream); color: var(--comic-dark);' }}">
                    {{ $tab['icon'] }} {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Comic Search Bar --}}
        <form method="GET" action="{{ route('admin.members.index') }}" class="comic-search-bar">
            @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">🔍 Pencarian</label>
                    <div class="comic-search-wrap">
                        <i class="ki-duotone ki-magnifier comic-search-icon"></i>
                        <input type="text" name="search" class="form-control form-control-solid"
                            placeholder="Cari nama, NIS, atau kode anggota..." value="{{ request('search') }}"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-comic w-100">
                        🔍 CARI
                    </button>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="comic-table-wrap">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th style="min-width:100px;">Kode</th>
                        <th style="min-width:180px;">Nama</th>
                        <th style="min-width:120px;">NIS/NIM</th>
                        <th style="min-width:70px;">QR</th>
                        <th style="min-width:100px;">Kelas</th>
                        <th style="min-width:120px;">WhatsApp</th>
                        <th style="min-width:80px;">Status</th>
                        <th class="text-end" style="min-width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($members as $member)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark" style="font-size:0.82rem;">{{ $member->member_code }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}"
                                        class="symbol symbol-35px flex-shrink-0"
                                        style="width:35px; height:35px; object-fit:cover; border-radius:0; border:2px solid var(--comic-dark);"/>
                                @else
                                    <div class="symbol symbol-35px flex-shrink-0">
                                        <div class="symbol-label fs-5 fw-bold"
                                            style="background:var(--comic-cream); color:var(--comic-dark); border:2px solid var(--comic-dark);">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    </div>
                                @endif
                                <span class="fw-bold text-dark" style="font-size:0.88rem;">{{ $member->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size:0.82rem;">{{ $member->nis_nim ?? '-' }}</span>
                        </td>
                        <td>
                            @if($member->qr_code)
                                <img src="{{ asset('storage/' . $member->qr_code) }}" alt="QR"
                                    style="width:40px; height:40px; object-fit:contain; border:2px solid #eee;"/>
                            @else
                                <form method="POST" action="{{ route('admin.members.regenerate-qr', $member) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-comic" style="padding:4px 8px !important; font-size:0.72rem !important;">
                                        <i class="ki-duotone ki-qrcode fs-4"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted" style="font-size:0.82rem;">{{ $member->class ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size:0.82rem;">{{ $member->whatsapp ?? '-' }}</span>
                        </td>
                        <td>
                            @if($member->status->value === 'pending')
                                <span class="badge badge-light-warning" style="font-size:0.72rem; border: 2px solid #f59e0b; background: rgba(245,158,11,0.15); color: #92400e;">
                                    ⏳ Pending
                                </span>
                            @elseif($member->status->value === 'active')
                                <span class="badge badge-light-success" style="font-size:0.72rem;">
                                    🟢 Aktif
                                </span>
                            @else
                                <span class="badge badge-light-secondary" style="font-size:0.72rem;">
                                    🔴 Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="action-group">
                                @if($member->status->value === 'pending')
                                    {{-- Approve / Reject for Pending --}}
                                    <form method="POST" action="{{ route('admin.members.approve', $member) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="action-btn" style="background: rgba(34,197,94,0.15); color: #15803d; border-color: #15803d; box-shadow: 3px 3px 0 #15803d;"
                                            onclick="return confirm('Setujui pendaftaran {{ $member->name }}?');">
                                            ✅ Setuju
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.members.reject', $member) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-delete btn-delete">
                                            ❌ Tolak
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.members.show', $member) }}"
                                        class="action-btn action-btn-detail" title="Detail">
                                        <i class="ki-duotone ki-eye fs-5"></i> Detail
                                    </a>
                                    <button type="button" class="action-btn action-btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-edit-member"
                                        data-member='@json($member)'
                                        title="Edit">
                                        <i class="ki-duotone ki-pencil fs-5"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.members.destroy', $member) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-delete btn-delete" title="Hapus">
                                            <i class="ki-duotone ki-trash fs-5"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="comic-empty">
                                <span class="empty-emoji">👥</span>
                                <div class="empty-title">TIDAK ADA ANGGOTA</div>
                                <div class="empty-sub">Tambah anggota baru untuk memulai</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Comic Pagination --}}
        @include('layouts.partials._pagination', ['paginator' => $members])
    </div>
</div>

@include('admin.members.partials.create-modal')
@include('admin.members.partials.edit-modal')
@endsection

@push('vendor-js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus anggota?',
                text: 'Data tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#FF3366',
            }).then(function (r) {
                if (r.isConfirmed) btn.closest('form').submit();
            });
        });
    });

    // Edit modal populate
    var editModal = document.getElementById('modal-edit-member');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (e) {
            var btn = e.relatedTarget;
            var member = JSON.parse(btn.getAttribute('data-member'));
            var form = editModal.querySelector('form');
            form.action = '/admin/members/' + member.id;

            form.querySelector('[name="member_code"]').value = member.member_code || '';
            form.querySelector('[name="name"]').value = member.name || '';
            form.querySelector('[name="class"]').value = member.class || '';
            form.querySelector('[name="nis_nim"]').value = member.nis_nim || '';
            form.querySelector('[name="major"]').value = member.major || '';
            form.querySelector('[name="address"]').value = member.address || '';
            form.querySelector('[name="whatsapp"]').value = member.whatsapp || '';
            form.querySelector('[name="email"]').value = member.email || '';
            form.querySelector('[name="status"]').value = member.status;
            form.querySelector('[name="remove_photo"]').value = '0';

            // Reset photo preview
            var preview = document.getElementById('edit-photo-preview');
            var removeWrap = document.getElementById('edit-photo-remove-wrap');
            var input = document.getElementById('edit-photo-input');
            if (input) input.value = '';

            if (member.photo) {
                preview.innerHTML = '<img src="/storage/' + member.photo + '" style="width:100%;height:100%;object-fit:cover;"/>';
                removeWrap.style.display = 'block';
            } else {
                preview.innerHTML = '<span style="font-size:2rem; color:#ccc;">📷</span>';
                removeWrap.style.display = 'block';
            }
        });
    }
});

// ── Photo Preview / Remove Helpers ──
function previewPhoto(input, previewId, removeWrapId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var preview = document.getElementById(previewId);
            preview.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;"/>';
            var removeWrap = document.getElementById(removeWrapId);
            if (removeWrap) removeWrap.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removePhoto(inputId, previewId, removeWrapId, isEdit) {
    var input = document.getElementById(inputId);
    var preview = document.getElementById(previewId);
    var removeWrap = document.getElementById(removeWrapId);
    if (input) input.value = '';
    if (preview) {
        preview.innerHTML = '<span style="font-size:2rem; color:#ccc;">📷</span>';
    }
    if (removeWrap) {
        removeWrap.style.display = 'none';
    }
    if (isEdit) {
        document.getElementById('edit-remove-photo-flag').value = '1';
    }
}
</script>
@endpush
