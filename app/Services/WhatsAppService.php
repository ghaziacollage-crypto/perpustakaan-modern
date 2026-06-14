<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Member;
use App\Models\WhatsAppLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

class WhatsAppService
{
    public function __construct(private readonly WhatsAppSettingsService $settings) {}

    public function sendMessage(?Member $member, string $phone, string $message): bool
    {
        return $this->sendMessageWithResult($member, $phone, $message)['success'];
    }

    public function sendMessageWithResult(?Member $member, string $phone, string $message): array
    {
        if (! $this->settings->isActive()) {
            $this->logFailure($member, $phone, $message, 'WhatsApp API tidak aktif');

            return $this->result(false, 'WhatsApp API tidak aktif');
        }

        $apiToken = $this->settings->getApiKey();
        if ($apiToken === '') {
            $this->logFailure($member, $phone, $message, 'API token belum diatur');

            return $this->result(false, 'API token belum diatur');
        }

        $sessionId = $this->settings->getSessionId();
        if ($sessionId === '') {
            $this->logFailure($member, $phone, $message, 'Session ID belum diatur');

            return $this->result(false, 'Session ID belum diatur');
        }

        $normalized = $this->normalizePhone($phone);
        if ($normalized === '') {
            $this->logFailure($member, $phone, $message, 'Nomor tidak valid');

            return $this->result(false, 'Nomor tidak valid');
        }

        $url = $this->settings->getSendMessageUrl();
        if ($url === '') {
            $this->logFailure($member, $normalized, $message, 'Base URL WhatsApp API belum diatur');

            return $this->result(false, 'Base URL WhatsApp API belum diatur');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(20)->post($url, [
                'session_id' => $sessionId,
                'to' => $normalized,
                'message' => $message,
            ]);
        } catch (ConnectionException $e) {
            $this->logFailure($member, $normalized, $message, 'Timeout / tidak bisa terhubung ke server WA: '.$e->getMessage());

            return $this->result(false, 'Timeout / tidak bisa terhubung ke server WA: '.$e->getMessage());
        } catch (Throwable $e) {
            $this->logFailure($member, $normalized, $message, 'Error WA API: '.$e->getMessage());

            return $this->result(false, 'Error WA API: '.$e->getMessage());
        }

        $body = $response->json() ?? $response->body();

        if ($response->successful()) {
            $this->logSuccess($member, $normalized, $message);

            return $this->result(true, 'Pesan terkirim.', $response->status(), $body);
        }

        $this->logFailure($member, $normalized, $message, is_string($body) ? $body : json_encode($body));

        return $this->result(false, 'Gagal mengirim pesan.', $response->status(), $body);
    }

    public function healthCheck(): array
    {
        $apiToken = $this->settings->getApiKey();
        $urls = $this->settings->getHealthUrls();

        if ($apiToken === '' || $urls === []) {
            return $this->result(false, 'Konfigurasi API belum lengkap.');
        }

        $last = null;
        foreach ($urls as $url) {
            try {
                $response = Http::withToken($apiToken)
                    ->acceptJson()
                    ->timeout(15)
                    ->get($url, ['session_id' => $this->settings->getSessionId()]);

                $body = $response->json() ?? $response->body();
                $last = $this->result($response->successful(), $response->successful() ? 'Health API aktif.' : 'Endpoint health belum berhasil.', $response->status(), [
                    'url' => $url,
                    'body' => $body,
                ]);

                if ($response->successful()) {
                    return $last;
                }
            } catch (Throwable $e) {
                $last = $this->result(false, $e->getMessage(), null, ['url' => $url]);
            }
        }

        return $last ?? $this->result(false, 'Health API tidak tersedia.');
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return '62'.$digits;
    }

    private function logSuccess(?Member $member, string $phone, string $message): void
    {
        WhatsAppLog::create([
            'member_id' => $member?->id,
            'phone' => $phone,
            'message' => $message,
            'status' => 'sent',
            'provider' => 'jokiin35',
            'sent_at' => now(),
        ]);
    }

    private function logFailure(?Member $member, string $phone, string $message, string $error): void
    {
        WhatsAppLog::create([
            'member_id' => $member?->id,
            'phone' => $phone,
            'message' => $message,
            'status' => 'failed',
            'provider' => 'jokiin35',
            'error_message' => $error,
        ]);
    }

    private function result(bool $success, string $message, ?int $statusCode = null, mixed $response = null): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'status_code' => $statusCode,
            'response' => $response,
        ];
    }
}
