<table>
    <thead>
        <tr>
            <th style="width:30px;">#</th>
            <th>Kode / Judul</th>
            <th>Kategori</th>
            <th>Penulis</th>
            <th style="width:40px; text-align:center;">Stok</th>
            <th style="width:70px; text-align:center;">Kondisi</th>
            <th style="width:70px; text-align:center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($books as $index => $book)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td>
                <strong>{{ $book->title ?? '-' }}</strong><br>
                <small style="color:#888;">{{ $book->book_code ?? '' }}</small>
            </td>
            <td>{{ $book->category?->name ?? '-' }}</td>
            <td>{{ $book->author ?? '-' }}</td>
            <td style="text-align:center;">{{ $book->stock ?? 0 }}</td>
            <td style="text-align:center;">
                @switch($book->kondisi->value ?? 'normal')
                    @case('normal') Normal @break
                    @case('rusak') Rusak @break
                    @case('hilang') Hilang @break
                    @default -
                @endswitch
            </td>
            <td style="text-align:center;">
                @if(($book->status->value ?? '') === 'available' && ($book->stock ?? 0) > 0)
                    Tersedia
                @else
                    Tidak
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; padding:20px; color:#999;">Tidak ada data buku pada filter ini.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<p style="text-align:right; font-size:9px; color:#999; margin-top:12px;">Total: {{ $books->count() }} buku</p>
