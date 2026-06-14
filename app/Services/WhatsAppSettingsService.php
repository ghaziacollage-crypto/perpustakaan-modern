<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\File;

class WhatsAppSettingsService
{
    private ?array $fileConfig = null;

    public function getApiKey(): string
    {
        return (string) (Setting::getValue('whatsapp_api_key', '') ?: config('whatsapp.api_token') ?: $this->getFileConfigValue('apiToken', ''));
    }

    public function getSender(): string
    {
        return (string) (Setting::getValue('whatsapp_sender', '') ?: config('whatsapp.sender') ?: $this->getFileConfigValue('phoneNumber', ''));
    }

    public function getSessionId(): string
    {
        return (string) (Setting::getValue('whatsapp_session_id', '') ?: config('whatsapp.session_id') ?: $this->getFileConfigValue('sessionId', ''));
    }

    public function isActive(): bool
    {
        return (bool) Setting::getValue('whatsapp_is_active', false)
            || (
                $this->getFileConfigValue('status') === 'connected'
                && $this->getSessionId() !== ''
                && $this->getApiKey() !== ''
            );
    }

    public function getReminderDays(): int
    {
        return (int) Setting::getValue('whatsapp_reminder_days', 1);
    }

    public function update(array $data): void
    {
        Setting::setValue('whatsapp_api_key', $data['api_key'] ?? '', 'text', 'whatsapp');
        Setting::setValue('whatsapp_sender', $data['sender'] ?? '', 'text', 'whatsapp');
        Setting::setValue('whatsapp_session_id', $data['session_id'] ?? '', 'text', 'whatsapp');
        Setting::setValue('whatsapp_is_active', (bool) ($data['is_active'] ?? false), 'boolean', 'whatsapp');
        Setting::setValue('whatsapp_reminder_days', (int) ($data['reminder_days'] ?? 1), 'number', 'whatsapp');
    }

    public function getBaseUrl(): string
    {
        return rtrim((string) (config('whatsapp.base_url') ?: $this->getFileConfigValue('apiBaseUrl', '')), '/');
    }

    public function getSendMessageUrl(): string
    {
        $baseUrl = $this->getBaseUrl();
        $path = (string) config('whatsapp.send_message_path', '/send-message');
        $endpoint = (string) $this->getFileConfigValue('sendMessageEndpoint', '');

        if (preg_match('/\s(\/\S+)$/', $endpoint, $matches)) {
            $path = $matches[1];
        }

        $normalizedPath = '/'.ltrim($path, '/');
        if (str_ends_with($baseUrl, '/api') && str_starts_with($normalizedPath, '/api/')) {
            $normalizedPath = substr($normalizedPath, 4);
        }

        return $baseUrl.rtrim($normalizedPath, '/');
    }

    public function getHealthUrls(): array
    {
        $baseUrl = $this->getBaseUrl();

        return collect(config('whatsapp.health_paths', []))
            ->map(fn (string $path): string => $baseUrl.'/'.ltrim($path, '/'))
            ->values()
            ->all();
    }

    public function getConnectionSummary(): array
    {
        return [
            'base_url' => $this->getBaseUrl(),
            'session_id' => $this->getSessionId(),
            'sender' => $this->getSender(),
            'has_token' => $this->getApiKey() !== '',
            'is_active' => $this->isActive(),
            'file_status' => (string) $this->getFileConfigValue('status', '-'),
            'file_last_seen' => (string) $this->getFileConfigValue('lastSeen', '-'),
        ];
    }

    private function getFileConfigValue(string $key, mixed $default = null): mixed
    {
        $config = $this->readFileConfig();

        return $config[$key] ?? $default;
    }

    private function readFileConfig(): array
    {
        if ($this->fileConfig !== null) {
            return $this->fileConfig;
        }

        $path = (string) config('whatsapp.config_path');
        if ($path === '' || ! File::exists($path)) {
            return $this->fileConfig = [];
        }

        $decoded = json_decode((string) File::get($path), true);

        return $this->fileConfig = is_array($decoded) ? $decoded : [];
    }
}
