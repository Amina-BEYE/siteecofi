<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/AuthModel.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

class AdminMailboxModel
{
    private ?string $lastError = null;

    public function testConfig(array $config): array
    {
        $config = $this->normalizeRuntimeConfig($config);

        if (!$this->hasImapConfig($config)) {
            return ['success' => false, 'message' => 'Configuration IMAP incomplète.'];
        }

        if (!$this->hasSmtpConfig($config)) {
            return ['success' => false, 'message' => 'Configuration SMTP incomplète.'];
        }

        $imapResult = $this->testImap($config);
        if (!$imapResult['success']) {
            return $imapResult;
        }

        $smtpResult = $this->testSmtp($config);
        if (!$smtpResult['success']) {
            return $smtpResult;
        }

        return ['success' => true, 'message' => 'Connexion réussie : IMAP et SMTP sont valides.'];
    }

    public function getInbox(int $userId, int $limit = 40): array
    {
        $imap = $this->openMailbox($userId);
        if (!$imap) {
            return [];
        }

        $messages = [];

        // Recherche avec UIDs réels
        $uids = imap_search($imap, 'ALL', SE_UID);
        if (!$uids) {
            imap_close($imap);
            return [];
        }

        // Les plus récents en premier
        rsort($uids);
        $uids = array_slice($uids, 0, $limit);

        foreach ($uids as $uid) {
            // FT_UID : on passe un UID, pas un numéro de séquence
            $overview = imap_fetch_overview($imap, (string) $uid, FT_UID)[0] ?? null;
            if (!$overview) {
                continue;
            }

            $excerpt = $this->plainText($this->fetchBody($imap, (int) $uid));

            $messages[] = [
                'uid' => (int) $uid,
                'from' => $this->decodeHeader((string) ($overview->from ?? '')),
                'subject' => $this->decodeHeader((string) ($overview->subject ?? 'Sans objet')),
                'date' => (string) ($overview->date ?? ''),
                'excerpt' => $this->shortText($excerpt, 180),
                'seen' => !empty($overview->seen),
            ];
        }

        imap_close($imap);

        return $messages;
    }

    public function getMessage(int $userId, int $uid): ?array
    {
        $imap = $this->openMailbox($userId);
        if (!$imap || $uid <= 0) {
            return null;
        }

        // FT_UID : on cherche directement par UID
        $overview = imap_fetch_overview($imap, (string) $uid, FT_UID)[0] ?? null;
        if (!$overview) {
            imap_close($imap);
            $this->lastError = 'Email introuvable.';
            return null;
        }

        $body = $this->plainText($this->fetchBody($imap, $uid));
        imap_close($imap);

        return [
            'uid' => $uid,
            'from' => $this->decodeHeader((string) ($overview->from ?? '')),
            'subject' => $this->decodeHeader((string) ($overview->subject ?? 'Sans objet')),
            'date' => (string) ($overview->date ?? ''),
            'body' => $body,
        ];
    }

    public function reply(int $userId, int $uid, string $body): array
    {
        $body = trim($body);
        if ($uid <= 0 || $body === '') {
            return ['success' => false, 'message' => 'Message de réponse obligatoire.'];
        }

        $config = $this->mailConfig($userId);
        if (!$config || !$this->hasSmtpConfig($config)) {
            return ['success' => false, 'message' => 'Configuration SMTP incomplète pour cet utilisateur.'];
        }

        $original = $this->getMessage($userId, $uid);
        if (!$original) {
            return ['success' => false, 'message' => 'Email original introuvable.'];
        }

        $recipient = $this->extractEmail((string) ($original['from'] ?? ''));
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Expéditeur invalide, réponse impossible.'];
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = (string) $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = (string) $config['smtp_username'];
            $mail->Password = (string) $config['smtp_password_plain'];
            $mail->SMTPSecure = $config['smtp_encryption'] === 'tls'
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = (int) $config['smtp_port'];
            $mail->CharSet = 'UTF-8';

            // Désactiver la vérification SSL (o2switch mutualisé)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom((string) $config['email_address'], (string) ($config['fullname'] ?? 'ECOFI'));
            $mail->addAddress($recipient);
            $mail->isHTML(false);
            $mail->Subject = $this->replySubject((string) ($original['subject'] ?? ''));
            $mail->Body = $body;
            $mail->send();

            return ['success' => true, 'message' => 'Réponse envoyée avec succès.'];
        } catch (Throwable $e) {
            error_log('[AdminMailboxModel] reply: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Impossible d\'envoyer la réponse SMTP.'];
        }
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    // -------------------------------------------------------------------------
    // Connexion IMAP
    // -------------------------------------------------------------------------

    private function openMailbox(int $userId)
    {
        if (!function_exists('imap_open')) {
            $this->lastError = 'L\'extension PHP IMAP n\'est pas activée sur ce serveur.';
            return false;
        }

        $config = $this->mailConfig($userId);
        if (!$config || !$this->hasImapConfig($config)) {
            $this->lastError = 'Configuration IMAP incomplète pour cet utilisateur.';
            return false;
        }

        $mailbox = $this->buildMailboxString($config);

        // Désactiver les erreurs PHP et utiliser imap_errors() à la place
        $imap = @imap_open($mailbox, (string) $config['imap_username'], (string) $config['imap_password_plain']);

        if (!$imap) {
            $errors = imap_errors() ?: [];
            $this->lastError = 'Connexion IMAP impossible : ' . implode(' | ', $errors);
            error_log('[AdminMailboxModel] imap_open failed: ' . implode(' | ', $errors));
            return false;
        }

        return $imap;
    }

    /**
     * Construit la chaîne de connexion IMAP.
     * /novalidate-cert est indispensable sur o2switch mutualisé
     * car le certificat est souvent émis pour le domaine principal
     * et non pour votre domaine personnalisé.
     */
    private function buildMailboxString(array $config): string
    {
        $encryption = ($config['imap_encryption'] === 'tls') ? 'tls' : 'ssl';

        return sprintf(
            '{%s:%d/imap/%s/novalidate-cert}INBOX',
            $config['imap_host'],
            (int) $config['imap_port'],
            $encryption
        );
    }

    // -------------------------------------------------------------------------
    // Config
    // -------------------------------------------------------------------------

    private function mailConfig(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }

        return (new AuthModel())->getMailConfigByUserId($userId);
    }

    private function hasImapConfig(array $config): bool
    {
        return trim((string) ($config['email_address'] ?? '')) !== ''
            && trim((string) ($config['imap_host'] ?? '')) !== ''
            && trim((string) ($config['imap_username'] ?? '')) !== ''
            && trim((string) ($config['imap_password_plain'] ?? '')) !== '';
    }

    private function hasSmtpConfig(array $config): bool
    {
        return trim((string) ($config['email_address'] ?? '')) !== ''
            && trim((string) ($config['smtp_host'] ?? '')) !== ''
            && trim((string) ($config['smtp_username'] ?? '')) !== ''
            && trim((string) ($config['smtp_password_plain'] ?? '')) !== '';
    }

    private function normalizeRuntimeConfig(array $config): array
    {
        $config['imap_port'] = (int) ($config['imap_port'] ?? 993);
        $config['smtp_port'] = (int) ($config['smtp_port'] ?? 465);
        $config['imap_encryption'] = strtolower(trim((string) ($config['imap_encryption'] ?? 'ssl'))) === 'tls' ? 'tls' : 'ssl';
        $config['smtp_encryption'] = strtolower(trim((string) ($config['smtp_encryption'] ?? 'ssl'))) === 'tls' ? 'tls' : 'ssl';
        $config['imap_password_plain'] = (string) ($config['imap_password_plain'] ?? $config['imap_password'] ?? '');
        $config['smtp_password_plain'] = (string) ($config['smtp_password_plain'] ?? $config['smtp_password'] ?? '');

        return $config;
    }

    // -------------------------------------------------------------------------
    // Tests de connexion
    // -------------------------------------------------------------------------

    private function testImap(array $config): array
    {
        if (!function_exists('imap_open')) {
            return ['success' => false, 'message' => 'L\'extension PHP IMAP n\'est pas activée sur ce serveur.'];
        }

        $mailbox = $this->buildMailboxString($config);

        $imap = @imap_open($mailbox, (string) $config['imap_username'], (string) $config['imap_password_plain'], OP_HALFOPEN);
        if (!$imap) {
            $error = implode(' | ', imap_errors() ?: []);
            return ['success' => false, 'message' => $this->connectionMessage($error, 'IMAP')];
        }

        imap_close($imap);
        return ['success' => true, 'message' => 'Connexion IMAP réussie.'];
    }

    private function testSmtp(array $config): array
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = (string) $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = (string) $config['smtp_username'];
            $mail->Password = (string) $config['smtp_password_plain'];
            $mail->SMTPSecure = $config['smtp_encryption'] === 'tls'
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = (int) $config['smtp_port'];
            $mail->Timeout = 8;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            if (!$mail->smtpConnect()) {
                return ['success' => false, 'message' => 'Connexion SMTP impossible. Vérifiez le serveur, le port ou la sécurité SSL/TLS.'];
            }

            $mail->smtpClose();
            return ['success' => true, 'message' => 'Connexion SMTP réussie.'];
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $this->connectionMessage($e->getMessage(), 'SMTP')];
        }
    }

    private function connectionMessage(string $error, string $protocol): string
    {
        $normalized = strtolower($error);

        if (str_contains($normalized, 'auth') || str_contains($normalized, 'login') || str_contains($normalized, 'password')) {
            return "Identifiants {$protocol} incorrects.";
        }

        if (str_contains($normalized, 'certificate') || str_contains($normalized, 'ssl') || str_contains($normalized, 'tls')) {
            return "Port ou sécurité SSL/TLS {$protocol} invalide.";
        }

        if (str_contains($normalized, 'host') || str_contains($normalized, 'timed out') || str_contains($normalized, 'connection')) {
            return "Serveur {$protocol} incorrect ou inaccessible.";
        }

        return "Connexion {$protocol} impossible. Vérifiez serveur, port, sécurité et identifiants.";
    }

    // -------------------------------------------------------------------------
    // Lecture du corps du message
    // -------------------------------------------------------------------------

    /**
     * Récupère le corps d'un message par son UID (et non son numéro de séquence).
     * On essaie dans l'ordre : texte brut (1.1), partie 1, puis corps complet.
     */
    private function fetchBody($imap, int $uid): string
    {
        // Essai 1 : texte brut dans un message multipart
        $body = imap_fetchbody($imap, (string) $uid, '1.1', FT_UID);

        // Essai 2 : première partie (message simple)
        if ($body === '' || $body === false) {
            $body = imap_fetchbody($imap, (string) $uid, '1', FT_UID);
        }

        // Essai 3 : corps brut complet
        if ($body === '' || $body === false) {
            $body = imap_body($imap, (string) $uid, FT_UID);
        }

        $body = quoted_printable_decode((string) $body);

        // Décodage base64 si nécessaire
        if (base64_encode(base64_decode($body, true)) === $body) {
            $body = base64_decode($body);
        }

        return (string) $body;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function plainText(string $value): string
    {
        $value = strip_tags($value);
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function decodeHeader(string $value): string
    {
        $parts = imap_mime_header_decode($value);
        $result = '';

        foreach ($parts as $part) {
            $charset = strtolower($part->charset ?? 'utf-8');
            $text = $part->text;

            if ($charset !== 'default' && $charset !== 'utf-8') {
                $text = mb_convert_encoding($text, 'UTF-8', $charset);
            }

            $result .= $text;
        }

        return trim($result);
    }

    private function extractEmail(string $value): string
    {
        if (preg_match('/<([^>]+)>/', $value, $matches)) {
            return trim($matches[1]);
        }

        return trim($value);
    }

    private function replySubject(string $subject): string
    {
        $subject = trim($subject) !== '' ? trim($subject) : 'Sans objet';
        return stripos($subject, 'Re:') === 0 ? $subject : 'Re: ' . $subject;
    }

    private function shortText(string $value, int $limit): string
    {
        if (function_exists('mb_strimwidth')) {
            return mb_strimwidth($value, 0, $limit, '...');
        }

        return strlen($value) > $limit ? substr($value, 0, $limit) . '...' : $value;
    }


    // Ajout à AdminMailboxModel — méthode getSent() + getSentMessage()
// À intégrer dans votre AdminMailboxModel existant

    // ─────────────────────────────────────────────────────────────────────────────
// Dans votre contrôleur / AdminMailboxModel, ajoutez ces deux méthodes :
// ─────────────────────────────────────────────────────────────────────────────

    /*
     * Retourne les N derniers emails du dossier Envoyés (Sent).
     *
     * Les noms de dossiers courants selon les hébergeurs :
     *   Sent / Sent Messages / Sent Items / INBOX.Sent / Éléments envoyés
     * On essaie dans l'ordre jusqu'à trouver le bon.
     */
    public function getSent(int $userId, int $limit = 40): array
    {
        $imap = $this->openSentMailbox($userId);
        if (!$imap) {
            return [];
        }

        $messages = [];
        $uids = imap_search($imap, 'ALL', SE_UID) ?: [];

        rsort($uids);
        $uids = array_slice($uids, 0, $limit);

        foreach ($uids as $uid) {
            $overview = imap_fetch_overview($imap, (string) $uid, FT_UID)[0] ?? null;
            if (!$overview) {
                continue;
            }

            $excerpt = $this->plainText($this->fetchBody($imap, (int) $uid));

            $messages[] = [
                'uid' => (int) $uid,
                'to' => $this->decodeHeader((string) ($overview->to ?? '')),
                'from' => $this->decodeHeader((string) ($overview->from ?? '')),
                'subject' => $this->decodeHeader((string) ($overview->subject ?? 'Sans objet')),
                'date' => (string) ($overview->date ?? ''),
                'excerpt' => $this->shortText($excerpt, 180),
                'seen' => true, // les envoyés sont toujours "lus"
            ];
        }

        imap_close($imap);

        return $messages;
    }

    /*
     * Retourne un email envoyé précis par UID.
     */
    public function getSentMessage(int $userId, int $uid): ?array
    {
        $imap = $this->openSentMailbox($userId);
        if (!$imap || $uid <= 0) {
            return null;
        }

        $overview = imap_fetch_overview($imap, (string) $uid, FT_UID)[0] ?? null;
        if (!$overview) {
            imap_close($imap);
            return null;
        }

        $body = $this->plainText($this->fetchBody($imap, $uid));
        imap_close($imap);

        return [
            'uid' => $uid,
            'to' => $this->decodeHeader((string) ($overview->to ?? '')),
            'from' => $this->decodeHeader((string) ($overview->from ?? '')),
            'subject' => $this->decodeHeader((string) ($overview->subject ?? 'Sans objet')),
            'date' => (string) ($overview->date ?? ''),
            'body' => $body,
            'seen' => true,
        ];
    }

    /*
     * Ouvre le dossier Envoyés en essayant plusieurs noms courants.
     * Compatible o2switch / cPanel / Roundcube.
     */
    private function openSentMailbox(int $userId)
    {
        if (!function_exists('imap_open')) {
            $this->lastError = 'L\'extension PHP IMAP n\'est pas activée sur ce serveur.';
            return false;
        }

        $config = $this->mailConfig($userId);
        if (!$config || !$this->hasImapConfig($config)) {
            $this->lastError = 'Configuration IMAP incomplète pour cet utilisateur.';
            return false;
        }

        $encryption = ($config['imap_encryption'] ?? 'ssl') === 'tls' ? 'tls' : 'ssl';
        $serverBase = sprintf(
            '{%s:%d/imap/%s/novalidate-cert}',
            $config['imap_host'],
            (int) $config['imap_port'],
            $encryption
        );

        // Noms de dossiers essayés dans l'ordre (o2switch/cPanel utilise "Sent")
        $sentFolders = [
            'Sent',
            'Sent Messages',
            'Sent Items',
            'INBOX.Sent',
            'Éléments envoyés',
        ];

        foreach ($sentFolders as $folder) {
            $imap = @imap_open(
                $serverBase . $folder,
                (string) $config['imap_username'],
                (string) $config['imap_password_plain']
            );

            if ($imap) {
                return $imap;
            }
        }

        $this->lastError = 'Dossier Envoyés introuvable (essayé : ' . implode(', ', $sentFolders) . ').';
        error_log('[AdminMailboxModel] openSentMailbox failed: ' . implode(' | ', imap_errors() ?: []));

        return false;
    }

    // ─────────────────────────────────────────────────────────────────────────────
// getStats() mis à jour pour inclure les envoyés :
// ─────────────────────────────────────────────────────────────────────────────

    public function getStats(?array $messages = null, ?array $sentMessages = null): array
    {
        $messages = $messages ?? [];
        $sentMessages = $sentMessages ?? [];

        return [
            'total' => count($messages) + count($sentMessages),
            'newsletter' => 0,
            'contact' => 0,
            'mailbox' => count($messages),
            'sent' => count($sentMessages),
        ];
    }
}