<?php

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Core/Database.php';

use App\Core\Database;

class ActualitesModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->ensureTable();
    }

    public function ensureTable(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS actualites (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(255) DEFAULT NULL,
            content LONGTEXT NOT NULL,
            category VARCHAR(120) NOT NULL DEFAULT 'Actualité',
            image VARCHAR(512) DEFAULT NULL,
            video VARCHAR(512) DEFAULT NULL,
            status VARCHAR(32) NOT NULL DEFAULT 'published',
            published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_actualites_status (status),
            INDEX idx_actualites_published (published_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function getPublishedActualites(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM actualites WHERE status = 'published' ORDER BY published_at DESC, id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActualites(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM actualites ORDER BY published_at DESC, id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addActualite(
        string $title,
        string $subtitle,
        string $content,
        string $image,
        string $video,
        string $category,
        string $status,
        string $publishedAt
    ): bool {
        $publishedAt = trim($publishedAt);
        if ($publishedAt === '') {
            $publishedAt = date('Y-m-d H:i:s');
        } else {
            $timestamp = strtotime($publishedAt);
            $publishedAt = $timestamp ? date('Y-m-d H:i:s', $timestamp) : date('Y-m-d H:i:s');
        }

        $stmt = $this->db->prepare("INSERT INTO actualites
            (title, subtitle, content, category, image, video, status, published_at)
            VALUES (:title, :subtitle, :content, :category, :image, :video, :status, :published_at)");

        return $stmt->execute([
            ':title' => $title,
            ':subtitle' => $subtitle !== '' ? $subtitle : null,
            ':content' => $content,
            ':category' => $category !== '' ? $category : 'Actualité',
            ':image' => $image !== '' ? $image : null,
            ':video' => $video !== '' ? $video : null,
            ':status' => in_array($status, ['published', 'draft'], true) ? $status : 'published',
            ':published_at' => $publishedAt,
        ]);
    }
}
