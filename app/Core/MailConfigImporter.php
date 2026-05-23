<?php

namespace App\Core {

use RuntimeException;
use SimpleXMLElement;

final class MailConfigImporter
{
    public static function parseMobileConfig(string $filePath): array
    {
        if (!is_file($filePath)) {
            throw new RuntimeException('Fichier mobileconfig introuvable.');
        }

        $xml = simplexml_load_file($filePath);
        if (!$xml instanceof SimpleXMLElement) {
            throw new RuntimeException('Le fichier mobileconfig n’est pas un XML/plist valide.');
        }

        $rootDict = $xml->dict ?? null;
        if (!$rootDict instanceof SimpleXMLElement) {
            throw new RuntimeException('Structure plist mobileconfig invalide.');
        }

        $payload = self::findMailPayload(self::plistDictToArray($rootDict));

        if ($payload === []) {
            throw new RuntimeException('Aucune configuration mail IMAP/SMTP trouvée dans le mobileconfig.');
        }

        return self::normalizePayload($payload);
    }

    private static function findMailPayload(array $data): array
    {
        $payloads = $data['PayloadContent'] ?? [];
        if (!is_array($payloads)) {
            return [];
        }

        foreach ($payloads as $payload) {
            if (!is_array($payload)) {
                continue;
            }

            $type = (string) ($payload['PayloadType'] ?? '');
            if ($type === 'com.apple.mail.managed' || isset($payload['IncomingMailServerHostName']) || isset($payload['OutgoingMailServerHostName'])) {
                return $payload;
            }
        }

        return [];
    }

    private static function normalizePayload(array $payload): array
    {
        $email = self::firstString($payload, ['EmailAddress', 'EmailAccountName', 'IncomingMailServerUsername']);
        $incomingSsl = self::firstBool($payload, ['IncomingMailServerUseSSL', 'IncomingMailServerSSL']);
        $outgoingSsl = self::firstBool($payload, ['OutgoingMailServerUseSSL', 'OutgoingMailServerSSL']);

        return [
            'email_address' => $email,
            'imap_host' => self::firstString($payload, ['IncomingMailServerHostName', 'IncomingMailServerHost']),
            'imap_port' => self::firstInt($payload, ['IncomingMailServerPortNumber', 'IncomingMailServerPort']),
            'imap_encryption' => $incomingSsl ? 'ssl' : '',
            'imap_username' => self::firstString($payload, ['IncomingMailServerUsername', 'IncomingMailServerUserName']) ?: $email,
            'imap_password' => '',
            'smtp_host' => self::firstString($payload, ['OutgoingMailServerHostName', 'OutgoingMailServerHost']),
            'smtp_port' => self::firstInt($payload, ['OutgoingMailServerPortNumber', 'OutgoingMailServerPort']),
            'smtp_encryption' => $outgoingSsl ? 'ssl' : '',
            'smtp_username' => self::firstString($payload, ['OutgoingMailServerUsername', 'OutgoingMailServerUserName']) ?: $email,
            'smtp_password' => '',
            'config_source' => 'mobileconfig',
        ];
    }

    private static function plistDictToArray(SimpleXMLElement $dict): array
    {
        $result = [];
        $children = $dict->children();
        $key = null;

        foreach ($children as $child) {
            $name = $child->getName();

            if ($name === 'key') {
                $key = (string) $child;
                continue;
            }

            if ($key === null) {
                continue;
            }

            $result[$key] = self::plistValue($child);
            $key = null;
        }

        return $result;
    }

    private static function plistArrayToArray(SimpleXMLElement $array): array
    {
        $result = [];
        foreach ($array->children() as $child) {
            $result[] = self::plistValue($child);
        }

        return $result;
    }

    private static function plistValue(SimpleXMLElement $node): mixed
    {
        return match ($node->getName()) {
            'dict' => self::plistDictToArray($node),
            'array' => self::plistArrayToArray($node),
            'integer' => (int) $node,
            'true' => true,
            'false' => false,
            default => trim((string) $node),
        };
    }

    private static function firstString(array $payload, array $keys): string
    {
        foreach ($keys as $key) {
            $value = trim((string) ($payload[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private static function firstInt(array $payload, array $keys): int
    {
        foreach ($keys as $key) {
            $value = (int) ($payload[$key] ?? 0);
            if ($value > 0) {
                return $value;
            }
        }

        return 0;
    }

    private static function firstBool(array $payload, array $keys): bool
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $payload)) {
                return filter_var($payload[$key], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return false;
    }
}
}

namespace {
    function parseMobileConfig(string $filePath): array
    {
        return \App\Core\MailConfigImporter::parseMobileConfig($filePath);
    }
}
