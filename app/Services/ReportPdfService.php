<?php

declare(strict_types=1);

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class ReportPdfService
{
    public function generatePdf(string $view, array $data, string $filename = 'laporan.pdf'): \Illuminate\Http\Response
    {
        $html = $this->wrapPdfContent($view, $data);

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    private function wrapPdfContent(string $view, array $data): string
    {
        $content = view($view, $data)->render();
        $kopPath = public_path('kop.png');
        $reportTitle = e($data['report_title'] ?? 'Laporan Perpustakaan');
        $reportSubtitle = e($data['report_subtitle'] ?? '');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8"/>
            <title>Laporan Perpustakaan</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    font-size: 11px;
                    color: #333;
                    line-height: 1.5;
                    margin: 0;
                    padding: 20px;
                }
                .kop {
                    text-align: center;
                    margin-bottom: 20px;
                    padding-bottom: 12px;
                    border-bottom: 3px solid #1A1A2E;
                }
                .kop img {
                    max-width: 100%;
                    height: auto;
                }
                .report-title {
                    text-align: center;
                    font-size: 16px;
                    font-weight: bold;
                    color: #1A1A2E;
                    margin: 16px 0;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .report-subtitle {
                    text-align: center;
                    font-size: 11px;
                    color: #666;
                    margin-bottom: 16px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 12px 0;
                }
                table th {
                    background: #1A1A2E;
                    color: #fff;
                    padding: 8px 10px;
                    font-weight: 600;
                    font-size: 10px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    text-align: left;
                    border: 1px solid #1A1A2E;
                }
                table td {
                    padding: 6px 10px;
                    border: 1px solid #ddd;
                    font-size: 10px;
                }
                table tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .signature {
                    text-align: right;
                    margin-top: 40px;
                    page-break-inside: avoid;
                }
                .signature .title {
                    font-weight: 600;
                }
                .signature .name {
                    font-weight: bold;
                    text-decoration: underline;
                    margin-top: 50px;
                }
                .badge {
                    display: inline-block;
                    padding: 2px 8px;
                    font-size: 9px;
                    font-weight: bold;
                    border-radius: 2px;
                }
                .badge-paid { background: #d4edda; color: #155724; }
                .badge-unpaid { background: #fff3cd; color: #856404; }
                .empty-state {
                    text-align: center;
                    padding: 30px;
                    color: #999;
                    font-style: italic;
                }
                @media print {
                    body { padding: 10px; }
                }
            </style>
        </head>
        <body>
            <div class="kop">
                <img src="{$kopPath}" alt="Kop Surat"/>
            </div>
            <div class="report-title">{$reportTitle}</div>
            <div class="report-subtitle">{$reportSubtitle}</div>
            {$content}
            <div class="signature">
                <div class="title">Kepala Perpustakaan</div>
                <div class="name">Ailen Rossa Nauda, M.Pd.</div>
                <div style="font-size:10px; margin-top:2px;">NIP. 196904061998022001</div>
            </div>
        </body>
        </html>
        HTML;
    }
}
