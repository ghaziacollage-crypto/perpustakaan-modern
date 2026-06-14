<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Services\ReportPdfService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MemberReportController extends Controller
{
    public function index(Request $request)
    {
        $classFilter = $request->input('class_filter');
        $statusFilter = $request->input('status_filter');
        $search = $request->input('search');

        $membersQuery = Member::query()
            ->withCount(['borrowings as total_borrowings' => fn ($q) => $q->whereNotNull('loan_date')]);

        if ($search) {
            $membersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('nis_nim', 'like', "%{$search}%");
            });
        }

        if ($classFilter) {
            $membersQuery->where('class', $classFilter);
        }

        if ($statusFilter) {
            $membersQuery->where('status', $statusFilter);
        }

        $totalMembers = Member::count();
        $totalActive = Member::where('status', 'active')->count();
        $totalInactive = Member::where('status', 'inactive')->count();
        $totalPending = Member::where('status', 'pending')->count();

        $members = $membersQuery->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $classes = Member::whereNotNull('class')->distinct()->pluck('class')->sort()->values();

        return view('admin.reports.members.index', compact(
            'members', 'totalMembers', 'totalActive', 'totalInactive', 'totalPending',
            'classes', 'classFilter', 'statusFilter', 'search',
        ));
    }

    public function exportPdf(Request $request, ReportPdfService $pdfService)
    {
        $classFilter = $request->input('class_filter');
        $statusFilter = $request->input('status_filter');
        $search = $request->input('search');

        $query = Member::query()
            ->withCount(['borrowings as total_borrowings' => fn ($q) => $q->whereNotNull('loan_date')])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_code', 'like', "%{$search}%")
                  ->orWhere('nis_nim', 'like', "%{$search}%");
            });
        }

        if ($classFilter) {
            $query->where('class', $classFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $members = $query->get();

        $totalMembers = $members->count();
        $totalActive = $members->where('status', 'active')->count();
        $totalInactive = $members->where('status', 'inactive')->count();
        $totalPending = $members->where('status', 'pending')->count();

        return $pdfService->generatePdf('admin.reports.members.pdf.main', [
            'members' => $members,
            'totalMembers' => $totalMembers,
            'totalActive' => $totalActive,
            'totalInactive' => $totalInactive,
            'totalPending' => $totalPending,
            'report_title' => 'Laporan Data Anggota',
            'report_subtitle' => '',
        ], 'data-anggota-' . date('Y-m-d') . '.pdf');
    }
}
