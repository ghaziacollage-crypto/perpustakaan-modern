<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'app_name',
                'value' => 'Aplikasi Perpustakaan',
                'type' => 'text',
                'group' => 'app',
                'label' => 'Nama Aplikasi',
            ],
            [
                'key' => 'app_description',
                'value' => 'Aplikasi manajemen perpustakaan',
                'type' => 'textarea',
                'group' => 'app',
                'label' => 'Deskripsi',
            ],
            [
                'key' => 'app_version',
                'value' => 'v1.0.0',
                'type' => 'text',
                'group' => 'app',
                'label' => 'Versi',
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'app',
                'label' => 'Logo',
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'app',
                'label' => 'Favicon',
            ],
            [
                'key' => 'whatsapp_api_key',
                'value' => '',
                'type' => 'text',
                'group' => 'whatsapp',
                'label' => 'API Key',
            ],
            [
                'key' => 'whatsapp_sender',
                'value' => '',
                'type' => 'text',
                'group' => 'whatsapp',
                'label' => 'Nomor Pengirim',
            ],
            [
                'key' => 'whatsapp_session_id',
                'value' => '',
                'type' => 'text',
                'group' => 'whatsapp',
                'label' => 'Session ID',
            ],
            [
                'key' => 'whatsapp_is_active',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'whatsapp',
                'label' => 'Aktifkan WhatsApp',
            ],
            [
                'key' => 'whatsapp_reminder_days',
                'value' => '1',
                'type' => 'number',
                'group' => 'whatsapp',
                'label' => 'Hari Reminder',
            ],
            [
                'key' => 'fine_amount_per_day',
                'value' => '1000',
                'type' => 'number',
                'group' => 'fine',
                'label' => 'Keterlambatan Per Hari',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
