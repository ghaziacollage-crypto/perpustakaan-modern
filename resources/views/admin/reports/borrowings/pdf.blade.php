<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal Pinjam</th>
            <th>Anggota</th>
            <th>Kelas</th>
            <th>Buku</th>
            <th>Jatuh Tempo</th>
            <th style="width:70px; text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($borrowings as $index => $b)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($b->loan_date)->isoFormat('D MMM Y') }}</td>
            <td>
                <strong>{{ $b->member->name ?? '-' }}</strong><br>
                <small style="color:#888;">{{ $b->member->member_code ?? '' }}</small>
            </td>
            <td>{{ $b->member->class ?? '-' }}</td>
            <td>
                @foreach($b->details as $detail)
                    {{ $detail->book->title ?? '-' }}<br>
                @endforeach
            </td>
            <td>{{ \Carbon\Carbon::parse($b->due_date)->isoFormat('D MMM Y') }}</td>
            <td style="text-align:center;">
                @if($b->status->value === 'returned')
                    <span class="badge badge-paid">Selesai</span>
                @elseif($b->isOverdue())
                    <span class="badge badge-unpaid">Terlambat</span>
                @else
                    <span class="badge badge-light">Aktif</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="empty-state">Tidak ada data peminjaman pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<p style="text-align:right; font-size:9px; color:#999; margin-top:12px;">Total peminjaman: {{ $borrowings->count() }} transaksi</p>
