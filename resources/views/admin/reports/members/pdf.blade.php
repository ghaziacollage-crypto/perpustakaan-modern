<table>
    <thead>
        <tr>
            <th style="width:30px;">#</th>
            <th style="width:90px;">Kode</th>
            <th>Nama</th>
            <th style="width:80px;">NIS/NIM</th>
            <th style="width:60px;">Kelas</th>
            <th style="width:60px;">Major</th>
            <th style="width:90px;">WhatsApp</th>
            <th style="width:60px; text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($members as $index => $member)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td style="font-size:9px; color:#888;">{{ $member->member_code ?? '-' }}</td>
            <td><strong>{{ $member->name ?? '-' }}</strong></td>
            <td style="font-size:9px;">{{ $member->nis_nim ?? '-' }}</td>
            <td style="text-align:center; font-size:9px;">{{ $member->class ?? '-' }}</td>
            <td style="font-size:9px;">{{ $member->major ?? '-' }}</td>
            <td style="font-size:9px;">{{ $member->whatsapp ?? '-' }}</td>
            <td style="text-align:center; font-size:9px;">
                @switch($member->status)
                    @case('active') Aktif @break
                    @case('inactive') Nonaktif @break
                    @case('pending') Pending @break
                    @default -
                @endswitch
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center; padding:20px; color:#999;">Tidak ada data anggota pada filter ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<p style="text-align:right; font-size:9px; color:#999; margin-top:12px;">Total: {{ $members->count() }} anggota</p>
