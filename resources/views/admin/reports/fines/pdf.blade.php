<table>
    <thead>
        <tr>
            <th style="width:30px;">#</th>
            <th>Anggota</th>
            <th>Kelas</th>
            <th>Buku</th>
            <th style="width:60px; text-align:center;">Hari</th>
            <th style="width:80px; text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($fines as $index => $fine)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td>
                <strong>{{ $fine->member->name ?? '-' }}</strong><br>
                <small style="color:#888;">{{ $fine->member->member_code ?? '' }}</small>
            </td>
            <td>{{ $fine->member->class ?? '-' }}</td>
            <td>
                @foreach($fine->borrowing->details as $detail)
                    {{ $detail->book->title ?? '-' }}<br>
                @endforeach
            </td>
            <td style="text-align:center; color:#d32f2f; font-weight:bold;">{{ $fine->days_late }} hari</td>
            <td style="text-align:center;">
                @if($fine->status === 'paid')
                    <span class="badge badge-paid">Lunas</span>
                @else
                    <span class="badge badge-unpaid">Belum</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="empty-state">Tidak ada data keterlambatan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<p style="text-align:right; font-size:9px; color:#999; margin-top:12px;">Total: {{ $fines->count() }} catatan keterlambatan</p>
