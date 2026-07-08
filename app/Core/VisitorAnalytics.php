<?php

namespace App\Core;

use PDO;
use Throwable;

class VisitorAnalytics
{
    private const TABLE_NAME = 'visitor_logs';
    private static bool $tableEnsured = false;

    public static function record(): void
    {
        if (PHP_SAPI === 'cli' || PHP_SAPI === 'cli-server') {
            return;
        }

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (!in_array($method, ['GET', 'HEAD'], true)) {
            return;
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_starts_with($requestUri, '/admin') || str_contains($requestUri, '/app/admin/')) {
            return;
        }

        try {
            $pdo = Database::getConnection();
            self::ensureTableExists($pdo);

            $sessionId = session_id() ?: '';
            $requestPath = substr($requestUri, 0, 255);
            $referer = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 255);
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $ipAddress = self::getClientIp();

            $stmt = $pdo->prepare(
                'INSERT INTO ' . self::TABLE_NAME . ' (session_id, request_path, referer, user_agent, ip_address) VALUES (:session_id, :request_path, :referer, :user_agent, :ip_address)'
            );

            $stmt->execute([
                ':session_id' => $sessionId,
                ':request_path' => $requestPath,
                ':referer' => $referer,
                ':user_agent' => $userAgent,
                ':ip_address' => $ipAddress,
            ]);
        } catch (Throwable $exception) {
            error_log('[VisitorAnalytics] ' . $exception->getMessage());
        }
    }

    public static function getSummary(int $days = 7): array
    {
        $pdo = Database::getConnection();
        self::ensureTableExists($pdo);

        $sql = <<<'SQL'
SELECT
    SUM(created_at >= CURDATE()) AS visits_today,
    SUM(created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)) AS visits_week,
    SUM(CASE WHEN created_at >= CURDATE() THEN 1 ELSE 0 END) AS visits_today_dup,
    COUNT(DISTINCT CASE WHEN created_at >= CURDATE() THEN session_id ELSE NULL END) AS uniques_today,
    COUNT(DISTINCT CASE WHEN created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) THEN session_id ELSE NULL END) AS uniques_week,
    COUNT(DISTINCT request_path) AS unique_paths
FROM 