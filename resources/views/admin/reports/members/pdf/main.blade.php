<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Data Anggota - {{ $config['library_name'] ?? 'Perpustakaan' }}</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.4; color: #222; }
        .report-header { text-align: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 3px double #222; }
        .report-header h2 { margin: 2px 0; font-size: 16pt; font-weight: bold; }
        .report-header p { margin: 2px 0; font-size: 10pt; color: #555; }
        .report-title { text-align: center; margin: 15px 0; }
        .report-title h3 { margin: 4px 0; font-size: 13pt; font-weight: bold; text-decoration: underline; }
        .report-filter { margin: 8px 0; font-size: 10pt; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #333; padding: 5px 8px; text-align: left; }
        table th { background-color: #eee; font-weight: bold; font-size: 9.5pt; text-transform: uppercase; text-align: center; }
        table td { font-size: 10pt; vertical-align: top; }
        table .text-center { text-align: center; }
        .summary-row { margin: 12px 0; padding: 8px; background: #f9f9f9; border-left: 4px solid #333; }
        .summary-row span { font-weight: bold; margin-right: 10px; }
        .report-footer { margin-top: 30px; text-align: right; font-size: 10pt; }
        .page-break { page-break-after: always; }
        .badge-active { color: green; font-weight: bold; }
        .badge-inactive { color: red; font-weight: bold; }
        .badge-pending { color: orange; font-weight: bold; }
    </style>
</head>
<body>
    <div class="report-header">
        <h2>{{ $config['library_name'] ?? 'Perpustakaan' }}</h2>
        <p>{{ $config['library_address'] ?? '' }}</p>
    </div>

    <div class="report-title">
        <h3>LAPORAN DATA ANGGOTA</h3>
    </div>

    @if(isset($filters))
    <div class="report-filter">
        @if(!empty($filters['search']))<span>Pencarian: {{ $filters['search'] }}</span><br>@endif
        @if(!empty($filters['class_filter']))<span>Kelas: {{ $filters['class_filter'] }}</span><br>@endif
        @if(!empty($filters['status_filter']))<span>Status: {{ $filters['status_filter'] }}</span><br>@endif
        <span>Periode: {{ \Carbon\Carbon::now()->format('d F Y') }}</span>
    </div>
    @endif

    <div class="summary-row">
        <span>Total Anggota: {{ number_format($totalMembers) }}</span> |
        <span>Aktif: {{ number_format($totalActive) }}</span> |
        <span>Tidak Aktif: {{ number_format($totalInactive) }}</span> |
        <span>Pending: {{ number_format($totalPending) }}</span>
    </div>

    @include('admin.reports.members.pdf.tables.main')

    <div class="report-footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>
