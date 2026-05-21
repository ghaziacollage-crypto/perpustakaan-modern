<!--begin::Menu-->
<div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
    data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
    data-kt-scroll-dependencies="#kt_aside_toolbar, #kt_aside_footer"
    data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="5px">

    <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
        id="#kt_aside_menu" data-kt-menu="true">

        {{-- Dashboard --}}
        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-home fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </span>
                <span class="menu-title">Dashboard</span>
            </a>
        </div>

        {{-- Section: Manajemen --}}
        <div class="menu-item pt-5">
            <div class="menu-content">
                <span class="menu-heading fw-bold text-uppercase fs-7">Manajemen</span>
            </div>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}" href="{{ route('admin.books.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-book fs-2"><span class="path1"></span><span class="path2"></span></i>
                </span>
                <span class="menu-title">Data Buku</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-category fs-2"><span class="path1"></span><span class="path2"></span></i>
                </span>
                <span class="menu-title">Kategori</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}" href="{{ route('admin.members.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-profile-user fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                </span>
                <span class="menu-title">Data Anggota</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-people fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </span>
                <span class="menu-title">Pengguna</span>
            </a>
        </div>

        {{-- Section: Transaksi --}}
        <div class="menu-item pt-5">
            <div class="menu-content">
                <span class="menu-heading fw-bold text-uppercase fs-7">Transaksi</span>
            </div>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}" href="{{ route('admin.borrowings.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-parcel fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </span>
                <span class="menu-title">Peminjaman</span>
                @if(isset($pendingBorrowingsCount) && $pendingBorrowingsCount > 0)
                    <span class="badge badge-warning ms-2" style="font-size:0.65rem; border-radius:0 !important;">{{ $pendingBorrowingsCount }}</span>
                @endif
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}" href="{{ route('admin.returns.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-delivery fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                </span>
                <span class="menu-title">Pengembalian</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.fines.*') ? 'active' : '' }}" href="{{ route('admin.fines.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-wallet fs-2"><span class="path1"></span><span class="path2"></span></i>
                </span>
                <span class="menu-title">Denda</span>
            </a>
        </div>

        {{-- Section: Sistem --}}
        <div class="menu-item pt-5">
            <div class="menu-content">
                <span class="menu-heading fw-bold text-uppercase fs-7">Sistem</span>
            </div>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-chart-simple fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </span>
                <span class="menu-title">Laporan</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-information fs-2"><span class="path1"></span><span class="path2"></span></i>
                </span>
                <span class="menu-title">Audit Log</span>
            </a>
        </div>

        {{-- Section: Pengaturan --}}
        <div class="menu-item pt-5">
            <div class="menu-content">
                <span class="menu-heading fw-bold text-uppercase fs-7">Pengaturan</span>
            </div>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.hero-slides.*') ? 'active' : '' }}" href="{{ route('admin.hero-slides.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-design fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                </span>
                <span class="menu-title">Hero Slide</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-gear fs-2"><span class="path1"></span><span class="path2"></span></i>
                </span>
                <span class="menu-title">Data Aplikasi</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link {{ request()->routeIs('admin.settings.whatsapp*') ? 'active' : '' }}" href="{{ route('admin.settings.whatsapp') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-message-text fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                </span>
                <span class="menu-title">WhatsApp API</span>
            </a>
        </div>

    </div>
</div>
<!--end::Menu-->
