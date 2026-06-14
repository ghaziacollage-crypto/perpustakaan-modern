<div class="card wa-panel mb-5">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="card-title">{{ $title }}</div>
        <span class="badge badge-light-primary" style="border-radius:0; border:2px solid var(--comic-dark);">{{ $items->count() }} data</span>
    </div>
    <div class="card-body p-4">
        <div class="comic-table-wrap">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width:45px;">Pilih</th>
                        <th>Nama Peminjam</th>
                        <th>Nomor WhatsApp</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Jatuh Tempo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $borrowing)
                    @php
                        $phone = $borrowing->member?->whatsapp;
                        $books = $borrowing->details
                            ->where('status', \App\Enums\BorrowingDetailStatus::Borrowed)
                            ->map(fn ($detail) => $detail->book?->title ?? '-')
                            ->implode(', ');
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox" name="selected[]" value="{{ $borrowing->id }}" {{ $phone ? '' : 'disabled' }}>
                        </td>
                        <td>{{ $borrowing->member?->name ?? '-' }}</td>
                        <td>
                            @if($phone)
                                {{ $phone }}
                            @else
                                <span class="wa-invalid">Nomor kosong</span>
                            @endif
                        </td>
                        <td>{{ $books ?: '-' }}</td>
                        <td>{{ $borrowing->loan_date->format('d M Y') }}</td>
                        <td>{{ $borrowing->due_date->format('d M Y') }}</td>
                        <td>{{ $statusLabel }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted fw-bold py-6">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
