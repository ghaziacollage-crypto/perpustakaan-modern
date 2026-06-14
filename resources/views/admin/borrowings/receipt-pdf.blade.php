<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Struk Peminjaman {{ $transaction_code }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Courier New', Courier, monospace;
    background: #fff;
    color: #000;
    font-size: 12px;
    line-height: 1.4;
    padding: 0;
}
.receipt-thermal {
    max-width: 320px;
    margin: 0 auto;
    padding: 12px 10px;
}

/* Header */
.receipt-header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 8px; margin-bottom: 8px; }
.receipt-header .app-name { font-size: 16px; font-weight: bold; letter-spacing: 1px; }
.receipt-header .app-address { font-size: 10px; color: #555; margin-top: 2px; }
.receipt-header .doc-title {
    font-size: 13px; font-weight: bold; margin-top: 6px; letter-spacing: 2px;
    border: 2px solid #000; padding: 3px 8px; display: inline-block;
}

/* Info */
.info-row { display: flex; justify-content: space-between; padding: 2px 0; font-size: 11px; }
.info-row .label { color: #555; }
.info-row .value { font-weight: bold; text-align: right; }

/* Divider */
.divider-thick { border: none; border-top: 2px dashed #000; margin: 8px 0; }

/* Transaction code */
.trx-code {
    text-align: center; font-size: 14px; font-weight: bold; letter-spacing: 2px;
    padding: 6px 0; border-top: 2px dashed #000; border-bottom: 2px dashed #000; margin: 6px 0;
}

/* Member */
.member-box { border: 2px solid #000; padding: 6px 8px; margin-bottom: 6px; }
.member-name { font-size: 13px; font-weight: bold; }
.member-code { font-size: 10px; color: #555; }

/* Books */
.book-list { margin: 6px 0; }
.book-item { display: flex; gap: 6px; padding: 4px 0; border-bottom: 1px dashed #ddd; font-size: 11px; }
.book-item:last-child { border-bottom: none; }
.book-num { font-weight: bold; min-width: 20px; }
.book-title { font-weight: bold; flex: 1; font-size: 11px; }
.book-code { color: #888; font-size: 10px; white-space: nowrap; }

/* Footer */
.footer-note { font-size: 10px; color: #555; text-align: center; margin-top: 8px; }
.footer-time { font-size: 10px; color: #888; text-align: center; margin-top: 4px; }

/* Status */
.status-badge { display: inline-block; padding: 2px 8px; font-weight: bold; font-size: 11px; letter-spacing: 1px; border: 2px solid #000; }
.status-active { background: #cce5ff; }
.status-returned { background: #d4edda; }
</style>
</head>
<body>
<div class="receipt-thermal">
    {{-- HEADER --}}
    <div style="text-align:center; margin-bottom:8px;">
        <img src="{{ asset('kop.png') }}" alt="Kop Surat" style="max-width:100%; height:auto;" />
    </div>
    <div class="receipt-header">
        <div class="doc-title">STRUK PEMINJAMAN</div>
    </div>

    {{-- TRANSACTION CODE --}}
    <div class="trx-code">{{ $transaction_code }}</div>

    {{-- MEMBER INFO --}}
    <div class="member-box">
        <div class="member-name">{{ $member['name'] }}</div>
        <div class="member-code">Kode: {{ $member['code'] }}{{ isset($member['class']) && $member['class'] ? ' | ' . $member['class'] : '' }}</div>
    </div>

    {{-- BORROWING DATES --}}
    <hr class="divider-thick">
    <div class="info-row">
        <span class="label">Tgl Pinjam</span>
        <span class="value">{{ $loan_date }}</span>
    </div>
    <div class="info-row">
        <span class="label">Jatuh Tempo</span>
        <span class="value" style="{{ $is_overdue ? 'color:#dc3545;' : '' }}">{{ $due_date }}</span>
    </div>
    @if($return_date)
    <div class="info-row">
        <span class="label">Tgl Kembali</span>
        <span class="value">{{ $return_date }}</span>
    </div>
    @endif
    <div class="info-row">
        <span class="label">Total Buku</span>
        <span class="value">{{ $total_books }} buku</span>
    </div>
    @if($status !== 'returned')
    <div class="info-row">
        <span class="label">Status</span>
        <span class="status-badge {{ $is_overdue ? 'status-overdue' : 'status-active' }}">
            {{ $is_overdue ? 'TERLAMBAT ' . ($borrowing->daysOverdue() ?? 0) . ' HR' : $status_label }}
        </span>
    </div>
    @endif
    <hr class="divider-thick">

    {{-- BOOK LIST --}}
    <div style="font-weight:bold; font-size:11px; margin-bottom:4px; letter-spacing:1px;">DAFTAR BUKU:</div>
    <div class="book-list">
        @foreach($books as $book)
        <div class="book-item">
            <span class="book-num">{{ $loop->iteration }}.</span>
            <div style="flex:1; min-width:0;">
                <div class="book-title">{{ Str::limit($book['title'], 30) }}</div>
                <div class="book-code">{{ $book['book_code'] }}{{ isset($book['author']) && $book['author'] ? ' | ' . Str::limit($book['author'], 20) : '' }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <hr class="divider-thick">

    {{-- NOTES --}}
    @if($notes)
    <div style="font-size:10px; color:#555; font-style:italic; padding:4px 0;">
        Catatan: {{ $notes }}
    </div>
    @endif

    {{-- FINE INFO --}}

    {{-- FOOTER --}}
    <hr class="divider-thick">
    <div class="footer-note">
        Harap kembalikan buku tepat waktu
    </div>
    <div class="footer-time">
        Dicetak: {{ $printed_at }}<br>
        Operator: {{ $printed_by }}
    </div>
</div>
</body>
</html>