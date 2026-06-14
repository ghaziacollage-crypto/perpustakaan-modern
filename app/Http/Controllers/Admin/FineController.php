<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\FineStatus;
use App\Http\Controllers\Controller;
use App\Models\Fine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(Request $request): View
    {
        $query = Fine::with(['member', 'borrowing.details.book']);

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('search')->toString()) {
            $query->whereHas('member', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $fines = $query->latest()->paginate(20)->withQueryString();

        // Summary stats
        $totalUnpaid = Fine::where('status', FineStatus::Unpaid)->sum('total_amount');
        $totalPaid = Fine::where('status', FineStatus::Paid)->sum('total_amount');
        $totalAll = Fine::sum('total_amount');

        return view('admin.fines.index', compact('fines', 'totalUnpaid', 'totalPaid', 'totalAll'));
    }

    public function markAsPaid(Fine $fine): RedirectResponse
    {
        $fine->update([
            'status' => FineStatus::Paid,
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Keterlambatan berhasil ditandai lunas.');
    }

    public function markAsUnpaid(Fine $fine): RedirectResponse
    {
        $fine->update([
            'status' => FineStatus::Unpaid,
            'paid_at' => null,
        ]);

        return redirect()->back()->with('success', 'Status keterlambatan berhasil dikembalikan.');
    }
}
