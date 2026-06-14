<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(): View
    {
        $query = Fine::with(['member', 'borrowing.details.book']);

        if ($status = request()->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = request()->string('search')->toString()) {
            $query->whereHas('member', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $fines = $query->latest()->paginate(20)->withQueryString();

        return view('admin.fines.index', compact('fines'));
    }
}
