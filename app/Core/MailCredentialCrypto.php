<?php

namespace App\Core;

final class MailCredentialCrypto
{
    private const CIPHER = 'aes-256-gcm';

    public static function encrypt(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($value, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv, $tag);

        if ($ciphertext === false) {
            throw new \RuntimeException('Chiffrement des identifiants mail impossible.');
        }

        return base64_encode(json_encode([
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'value' => base64_encode($ciphertext),
        ], JSON_THROW_ON_ERROR));
    }

    public static function decrypt(?string $payload): string
    {
        $payload = trim((string) $payload);
        if ($payload === '') {
            return '';
        }

        try {
            $decoded = json_decode((string) base64_decode($payload, true), true, 512, JSON_THROW_ON_ERROR);
            $iv = base64_decode((string) ($decoded['iv'] ?? ''), true);
            $tag = base64_decode((string) ($decoded['tag'] ?? ''), true);
            $ciphertext = base64_decode((string) ($decoded['value'] ?? ''), true);

            if ($iv === false || $tag === false || $ciphertext === false) {
                return '';
            }

            $plain = openssl_decrypt($ciphertext, self::CIPHER, self::key(), OPENSSL_RAW_DATA, $iv, $tag);

            return $plain === false ? '' : $plain;
        } catch (\Throwable $e) {
            error_log('[MailCredentialCrypto] decrypt: ' . $e->getMessage());
            return '';
        }
    }

    private static function key(): string
    {
        $secret = $_ENV['MAIL_CREDENTIAL_KEY'] ?? getenv('MAIL_CREDENTIAL_KEY') ?: '';

        if ($secret === '' && defined('DB_PASS')) {
            $secret = DB_PASS;
        }

        if ($secret === '') {
            $secret = __DIR__;
        }

        return hash('sha256', $secret, true);
    }
}
