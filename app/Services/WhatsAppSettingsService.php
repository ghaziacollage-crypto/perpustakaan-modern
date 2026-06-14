<?php

declare(strict_types=1);

namespace App\Services;

class WhatsAppSettingsService
{
    public function getApiKey(): string
    {
        return (string) config('whatsapp.api_token', '');
    }

    public function getSender(): string
    {
        return (string) config('whatsapp.sender', '');
    }

    public function getSessionId(): string
    {
        return (string) config('whatsapp.session_id', '');
    }

    public function isActive(): bool
    {
        return (bool) config('whatsapp.is_active', true)
            && $this->getBaseUrl() !== ''
            && $this->getApiKey() !== ''
            && $this->getSessionId() !== '';
    }

    public function getReminderDays(): int
    {
        return (int) config('whatsapp.reminder_days', 1);
    }

    public function update(array $data): void
    {
        // WhatsApp API configuration is intentionally managed from .env only.
    }

    public function getBaseUrl(): string
    {
        return rtrim((string) config('whatsapp.base_url', ''), '/');
    }

    public function getSendMessageUrl(): string
    {
        $baseUrl = $this->getBaseUrl();
        if ($baseUrl === '') {
            return '';
        }

        $path = '/'.ltrim((string) config('whatsapp.send_message_path', '/send-message'), '/');
        if (str_ends_with($baseUrl, '/api') && str_starts_with($path, '/api/')) {
            $path = substr($path, 4);
        }

        return $baseUrl.rtrim($path, '/');
    }

    public function getHealthUrls(): array
    {
        $baseUrl = $this->getBaseUrl();
        if ($baseUrl === '') {
            return [];
        }

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
            'config_source' => '.env',
        ];
    }
}
