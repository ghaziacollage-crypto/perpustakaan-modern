<table>
    <thead>
        <tr>
            <th style="width:30px; text-align:center;">No</th>
            <th>Nama</th>
            <th style="width:100px;">Kode</th>
            <th style="width:80px;">Kelas</th>
            <th style="width:80px;">NIS/NIM</th>
            <th style="width:70px;">Status</th>
            <th style="width:50px; text-align:center;">Pinjam</th>
            <th style="width:80px;">Tgl Daftar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($members as $index => $member)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td>{{ $member->name }}</td>
            <td>{{ $member->member_code }}</td>
            <td>{{ $member->class ?? '-' }}</td>
            <td>{{ $member->nis_nim ?? '-' }}</td>
            <td>
                @if($member->status === 'active')
                    <span class="badge-active">Aktif</span>
                @elseif($member->status === 'inactive')
                    <span class="badge-inactive">Tidak Aktif</span>
                @else
                    <span class="badge-pending">Pending</span>
                @endif
            </td>
            <td style="text-align:center;">{{ $member->total_borrowings ?? 0 }}</td>
            <td>{{ $member->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
