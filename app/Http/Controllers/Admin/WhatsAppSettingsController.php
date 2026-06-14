<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class WhatsAppSettingsController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()
            ->route('admin.whatsapp.index')
            ->with('info', 'Konfigurasi WhatsApp API sekarang hanya dibaca dari file .env.');
    }

    public function update(): RedirectResponse
    {
        return $this->index();
    }

    public function test(): RedirectResponse
    {
        return $this->index();
    }
}
