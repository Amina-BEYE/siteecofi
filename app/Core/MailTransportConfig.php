<?php

namespace App\Core;

class MailTransportConfig
{
    public static function smtpUser(): string
    {
        return self::firstValue([
            $_ENV['SMTP_USER'] ?? null,
            getenv('SMTP_USER') ?: null,
            Settings::get('smtp_user'),
            Settings::get('contact_email'),
        ]);
    }

    public static function smtpPassword(): string
    {
        return self::firstValue([
            $_ENV['SMTP_PASSWORD'] ?? null,
            getenv('SMTP_PASSWORD') ?: null,
            $_ENV['SMTP_PASS'] ?? null,
            getenv('SMTP_PASS') ?: null,
            Settings::get('smtp_password'),
            self::legacyQuotePassword(),
        ]);
    }

    public static function smtpHost(): string
    {
        return Settings::get('smtp_host', 'smtp.gmail.com');
    }

    public static function smtpPort(): int
    {
        $port = (int) Settings::get('smtp_port', '587');
        return $port > 0 ? $port : 587;
    }

    public static function smtpEncryption(): string
    {
        $port = self::smtpPort();
        $encryption = strtolower(trim(Settings::get('smtp_encryption', $port === 465 ? 'ssl' : 'tls')));
        return $encryption === 'ssl' ? 'ssl' : 'tls';
    }

    public static function fromName(): string
    {
        return Settings::get('smtp_from_name', 'ECOFI Construction');
    }

    private static function firstValue(array $values): string
    {
        foreach ($values as $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private static function legacyQuotePassword(): string
    {
        return 'rocu nndd vkyu usaz';
    }
}
