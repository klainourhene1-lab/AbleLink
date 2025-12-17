<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class EventsController extends Controller {
    private \PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
    // ========== HELPER METHODS (SQL QUERIES) ==========
    
    private function hasColumn(string $table, string $column): bool {
        try {
            $stmt = $this->pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
            $stmt->execute([$column]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function ensureTables(): void {
        $sql = 'CREATE TABLE IF NOT EXISTS `events` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(255) NOT NULL,
            `company` VARCHAR(255) NULL,
            `description` TEXT NULL,
            `accessibility_type` VARCHAR(100) NULL,
            `location` VARCHAR(255) NULL,
            `start_at` DATETIME NULL,
            `end_at` DATETIME NULL,
            `status` ENUM("draft","published","archived") DEFAULT "published",
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_events_status` (`status`),
            INDEX `idx_events_start_at` (`start_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
        
        try {
            $this->pdo->exec($sql);
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS `event_evaluations` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `event_id` INT NOT NULL,
                `user_name` VARCHAR(255) NULL,
                `rating` INT NOT NULL,
                `comment` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_ev_event_id` (`event_id`),
                CONSTRAINT `fk_ev_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
            $this->pdo->exec('CREATE TABLE IF NOT EXISTS `event_participations` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `event_id` INT NOT NULL,
                `user_name` VARCHAR(255) NULL,
                `status` ENUM("registered","attended","cancelled") DEFAULT "registered",
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_ep_event_id` (`event_id`),
                CONSTRAINT `fk_ep_event` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        } catch (\PDOException $e) {
            // Tables already exist or error creating them
        }
    }
    
    private function listEvents(array $opts = []): array {
        $this->ensureTables();
        $q = trim((string)($opts['q'] ?? ''));
        $access = trim((string)($opts['access'] ?? ''));
        $company = trim((string)($opts['company'] ?? ''));
        $scope = trim((string)($opts['scope'] ?? 'upcoming'));
        $limit = (int)($opts['limit'] ?? 12);
        $offset = (int)($opts['offset'] ?? 0);
        $where = [];
        $params = [];
        if ($q !== '') {
            $where[] = '(e.title LIKE ? OR e.location LIKE ? OR e.company LIKE ?)';
            $like = '%'.$q.'%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($access !== '') { $where[] = 'e.accessibility_type = ?'; $params[] = $access; }
        if ($company !== '') { $where[] = 'e.company LIKE ?'; $params[] = '%'.$company.'%'; }
        if ($scope === 'upcoming') { $where[] = '(e.start_at IS NULL OR e.start_at >= NOW())'; }
        elseif ($scope === 'past') { $where[] = '(e.end_at IS NOT NULL AND e.end_at < NOW())'; }
        $sql = 'SELECT e.*, COALESCE(AVG(ev.rating),0) AS rating_avg, COUNT(ev.id) AS evaluations
                FROM events e
                LEFT JOIN event_evaluations ev ON ev.event_id = e.id';
        if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
        $sql .= ' GROUP BY e.id ORDER BY e.start_at ASC, e.created_at DESC';
        if ($limit > 0) { $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset; }
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function accessibilityTypes(): array {
        $this->ensureTables();
        try {
            $stmt = $this->pdo->query('SELECT DISTINCT accessibility_type FROM events WHERE accessibility_type IS NOT NULL AND accessibility_type <> "" ORDER BY accessibility_type');
            return array_map(fn($r)=>$r['accessibility_type'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function companies(): array {
        $this->ensureTables();
        try {
            $stmt = $this->pdo->query('SELECT DISTINCT company FROM events WHERE company IS NOT NULL AND company <> "" ORDER BY company');
            return array_map(fn($r)=>$r['company'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function submitEvaluation(int $eventId, string $userName, int $rating, string $comment): bool {
        $this->ensureTables();
        try {
            $stmt = $this->pdo->prepare('INSERT INTO event_evaluations (event_id, user_name, rating, comment) VALUES (?, ?, ?, ?)');
            return $stmt->execute([$eventId, $userName, $rating, $comment]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function registerParticipation(int $eventId, string $userName): bool {
        $this->ensureTables();
        try {
            $stmt = $this->pdo->prepare('INSERT INTO event_participations (event_id, user_name) VALUES (?, ?)');
            return $stmt->execute([$eventId, $userName]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    // ========== PUBLIC CONTROLLER ACTIONS ==========
    
    public function index(): void {
        $q = trim($_GET['q'] ?? '');
        $access = trim($_GET['access'] ?? '');
        $company = trim($_GET['company'] ?? '');
        $scope = trim($_GET['scope'] ?? 'upcoming');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per = max(6, (int)($_GET['per'] ?? 6));
        
        $events = $this->listEvents([
            'q'=>$q,
            'access'=>$access,
            'company'=>$company,
            'scope'=>$scope,
            'limit'=>$per,
            'offset'=>($page-1)*$per
        ]);
        $types = $this->accessibilityTypes();
        $companies = $this->companies();
        
        $this->render('events/index', [
            'events' => $events,
            'q' => $q,
            'access' => $access,
            'company' => $company,
            'scope' => $scope,
            'types' => $types,
            'companies' => $companies,
            'page' => $page,
            'per' => $per
        ]);
    }

    public function evaluate(): void {
        $event_id = (int)($_POST['event_id'] ?? 0);
        $user_name = trim($_POST['user_name'] ?? 'Utilisateur');
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $comment = trim($_POST['comment'] ?? '');
        
        if ($event_id) {
            $this->submitEvaluation($event_id, $user_name, $rating, $comment);
        }
        
        header('Location: /projetweb/ablelink/events');
    }

    public function register(): void {
        $event_id = (int)($_POST['event_id'] ?? 0);
        $user_name = trim($_POST['user_name'] ?? 'Utilisateur');
        
        if ($event_id) {
            $this->registerParticipation($event_id, $user_name);
        }
        
        header('Location: /projetweb/ablelink/events');
    }
}
