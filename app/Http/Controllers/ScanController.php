<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class ScanController extends Controller
{
    /**
     * GET /scan
     * Kiosk page for library counter — scan member + scan books
     */
    public function index(): View
    {
        return view('scan.kiosk');
    }
}
