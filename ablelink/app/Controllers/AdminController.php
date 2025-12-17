<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class AdminController extends Controller {
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
    
    private function contentField(): string {
        if ($this->hasColumn('success_stories', 'content')) return 'content';
        if ($this->hasColumn('success_stories', 'description')) return 'description';
        return 'content';
    }
    
    private function ensureStatusColumn(): void {
        if (!$this->hasColumn('success_stories', 'status')) {
            try {
                $this->pdo->exec('ALTER TABLE `success_stories` ADD COLUMN `status` ENUM("pending","approved","rejected") DEFAULT "pending" AFTER `likes`');
            } catch (\PDOException $e) { }
        }
    }
    
    private function allForAdmin(): array {
        try {
            $stmt = $this->pdo->query('SELECT * FROM success_stories ORDER BY created_at DESC');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function pending(): array {
        try {
            $hasStatus = $this->hasColumn('success_stories', 'status');
            $sql = $hasStatus ? 'SELECT * FROM success_stories WHERE status = "pending" OR status IS NULL ORDER BY created_at DESC'
                              : 'SELECT * FROM success_stories ORDER BY created_at DESC';
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function totalLikes(): int {
        try {
            $stmt = $this->pdo->query('SELECT COALESCE(SUM(likes),0) AS s FROM success_stories');
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int)($row['s'] ?? 0);
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    private function countAllComments(): int {
        try {
            $stmt = $this->pdo->query('SELECT COUNT(*) AS c FROM comments');
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int)($row['c'] ?? 0);
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    private function listAdminWithFilters(array $opts = []): array {
        $q = trim((string)($opts['q'] ?? ''));
        $status = (string)($opts['status'] ?? 'all');
        $sort = (string)($opts['sort'] ?? 'recent');
        $start = trim((string)($opts['start'] ?? ''));
        $end = trim((string)($opts['end'] ?? ''));
        $where = [];
        $params = [];
        if ($status !== 'all' && $this->hasColumn('success_stories', 'status')) {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        $contentCol = $this->contentField();
        if ($q !== '') {
            $where[] = '(title LIKE ? OR ' . $contentCol . ' LIKE ? OR author LIKE ?)';
            $like = '%'.$q.'%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($start !== '') { $where[] = 'created_at >= ?'; $params[] = $start . ' 00:00:00'; }
        if ($end !== '') { $where[] = 'created_at <= ?'; $params[] = $end . ' 23:59:59'; }
        $order = 'created_at DESC';
        if ($sort === 'likes') { $order = 'likes DESC, created_at DESC'; }
        elseif ($sort === 'title') { $order = 'title ASC'; }
        $sql = 'SELECT * FROM success_stories';
        if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
        $sql .= ' ORDER BY ' . $order;
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function findStory(int $id): ?array {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM success_stories WHERE id = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }
    
    private function approveStory(int $id): bool {
        try {
            $this->ensureStatusColumn();
            $stmt = $this->pdo->prepare('UPDATE success_stories SET status = "approved" WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function rejectStory(int $id): bool {
        try {
            $this->ensureStatusColumn();
            $stmt = $this->pdo->prepare('UPDATE success_stories SET status = "rejected" WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function updateStatusByIds(array $ids, string $status): bool {
        if (empty($ids)) return false;
        $this->ensureStatusColumn();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        try {
            $stmt = $this->pdo->prepare('UPDATE success_stories SET status = ? WHERE id IN (' . $placeholders . ')');
            return $stmt->execute(array_merge([$status], $ids));
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function deleteByIds(array $ids): bool {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        try {
            $stmt = $this->pdo->prepare('DELETE FROM success_stories WHERE id IN (' . $placeholders . ')');
            return $stmt->execute($ids);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function statusCounts(): array {
        $hasStatus = $this->hasColumn('success_stories', 'status');
        try {
            if ($hasStatus) {
                $stmt = $this->pdo->prepare('SELECT status, COUNT(*) AS c FROM success_stories GROUP BY status');
                $stmt->execute();
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $out = ['approved'=>0,'pending'=>0,'rejected'=>0];
                foreach ($rows as $r) { $out[$r['status']] = (int)$r['c']; }
                $out['total'] = array_sum($out);
                return $out;
            } else {
                $stmt = $this->pdo->prepare('SELECT COUNT(*) AS c FROM success_stories');
                $stmt->execute();
                $t = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['c'] ?? 0);
                return ['approved'=>0,'pending'=>0,'rejected'=>0,'total'=>$t];
            }
        } catch (\PDOException $e) {
            return ['approved'=>0,'pending'=>0,'rejected'=>0,'total'=>0];
        }
    }
    
    private function categoryDistribution(): array {
        $hasCategory = $this->hasColumn('success_stories', 'category');
        if (!$hasCategory) return [];
        try {
            $stmt = $this->pdo->prepare('SELECT category, COUNT(*) AS c FROM success_stories GROUP BY category ORDER BY c DESC');
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function monthlyProgression(int $months = 6, string $status = 'approved'): array {
        $months = max(1, min(24, $months));
        $hasStatus = $this->hasColumn('success_stories', 'status');
        try {
            $sql = 'SELECT DATE_FORMAT(created_at, "%Y-%m") AS ym, COUNT(*) AS c FROM success_stories';
            $params = [];
            $where = [];
            if ($hasStatus && in_array($status, ['approved','pending','rejected'], true)) {
                $where[] = 'status = ?';
                $params[] = $status;
            }
            if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
            $sql .= ' GROUP BY ym ORDER BY ym DESC LIMIT ' . (int)$months;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = array_reverse($stmt->fetchAll(\PDO::FETCH_ASSOC));
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    // Comment-related SQL methods
    private function ensureCommentColumns(): void {
        if (!$this->hasColumn('comments', 'likes')) {
            try { $this->pdo->exec('ALTER TABLE `comments` ADD COLUMN `likes` INT DEFAULT 0'); } catch (\PDOException $e) { }
        }
        if (!$this->hasColumn('comments', 'parent_id')) {
            try { $this->pdo->exec('ALTER TABLE `comments` ADD COLUMN `parent_id` INT NULL'); } catch (\PDOException $e) { }
        }
        if (!$this->hasColumn('comments', 'reported')) {
            try { $this->pdo->exec('ALTER TABLE `comments` ADD COLUMN `reported` TINYINT(1) DEFAULT 0'); } catch (\PDOException $e) { }
        }
    }
    
    private function reportedComments(int $limit = 20): array {
        try {
            $this->ensureCommentColumns();
            $hasContent = $this->hasColumn('comments', 'content');
            $select = $hasContent ? 'content' : 'comment_text';
            $sql = 'SELECT c.id, c.story_id, c.author, c.' . $select . ' AS content, c.likes, c.created_at, s.title 
                    FROM comments c 
                    INNER JOIN success_stories s ON s.id = c.story_id 
                    WHERE c.reported = 1 
                    ORDER BY c.created_at DESC 
                    LIMIT ' . (int)$limit;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function listAllComments(array $opts = []): array {
        try {
            $this->ensureCommentColumns();
            $hasContent = $this->hasColumn('comments', 'content');
            $select = $hasContent ? 'c.content' : 'c.comment_text';
            $where = [];
            $params = [];
            $storyId = isset($opts['story_id']) ? (int)$opts['story_id'] : 0;
            $reported = isset($opts['reported']) ? trim((string)$opts['reported']) : '';
            $sort = isset($opts['sort']) ? (string)$opts['sort'] : 'recent';
            if ($storyId > 0) { $where[] = 'c.story_id = ?'; $params[] = $storyId; }
            if ($reported === '1') { $where[] = 'c.reported = 1'; }
            elseif ($reported === '0') { $where[] = 'c.reported = 0'; }
            $order = 'c.created_at DESC';
            if ($sort === 'oldest') { $order = 'c.created_at ASC'; }
            elseif ($sort === 'best' && $this->hasColumn('comments', 'likes')) { $order = 'c.likes DESC, c.created_at DESC'; }
            $sql = 'SELECT c.id, c.story_id, s.title AS story_title, c.author, ' . $select . ' AS content, c.likes, c.reported, c.created_at
                    FROM comments c INNER JOIN success_stories s ON s.id = c.story_id';
            if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
            $sql .= ' ORDER BY ' . $order;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function unreportComment(int $id): bool {
        try {
            $this->ensureCommentColumns();
            $stmt = $this->pdo->prepare('UPDATE comments SET reported = 0 WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function deleteCommentById(int $id): bool {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function commentsForStory(int $storyId, string $sort = 'recent'): array {
        try {
            $this->ensureCommentColumns();
            $hasContent = $this->hasColumn('comments', 'content');
            $hasLikes = $this->hasColumn('comments', 'likes');
            $select = $hasContent ? 'content' : 'comment_text';
            $likes = $hasLikes ? 'likes' : '0 AS likes';
            $order = 'created_at DESC';
            if ($sort === 'oldest') { $order = 'created_at ASC'; }
            elseif ($sort === 'best') { $order = 'likes DESC, created_at DESC'; }
            $sql = 'SELECT id, story_id, author, ' . $select . ' AS content, ' . $likes . ', created_at FROM comments WHERE story_id = ? AND (parent_id IS NULL OR parent_id = 0) AND reported = 0 ORDER BY ' . $order;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$storyId]);
            $top = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($top as &$c) {
                $c['replies'] = $this->commentReplies($c['id'], $sort);
            }
            return $top;
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function commentReplies(int $parentId, string $sort = 'recent'): array {
        try {
            $hasContent = $this->hasColumn('comments', 'content');
            $hasLikes = $this->hasColumn('comments', 'likes');
            $select = $hasContent ? 'content' : 'comment_text';
            $likes = $hasLikes ? 'likes' : '0 AS likes';
            $order = 'created_at DESC';
            if ($sort === 'oldest') { $order = 'created_at ASC'; }
            elseif ($sort === 'best') { $order = 'likes DESC, created_at DESC'; }
            $sql = 'SELECT id, story_id, author, ' . $select . ' AS content, ' . $likes . ', created_at FROM comments WHERE parent_id = ? AND reported = 0 ORDER BY ' . $order;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$parentId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    // ========== PUBLIC CONTROLLER ACTIONS ==========
    
    public function index(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = 'auto';
        }
        
        $allStories = $this->allForAdmin();
        $pendingStories = $this->pending();
        $totalLikes = $this->totalLikes();
        $totalComments = $this->countAllComments();
        
        $stats = [
            'stories' => count($allStories),
            'pending' => count($pendingStories),
            'approved' => count(array_filter($allStories, fn($s) => ($s['status'] ?? '') === 'approved')),
            'comments' => $totalComments,
            'likes' => $totalLikes,
            'users' => 0,
        ];
        
        extract(['stats' => $stats, 'pendingStories' => $pendingStories]);
        $viewFile = __DIR__ . '/../Views/admin/index.php';
        include $viewFile;
    }
    
    public function allStories(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $q = trim($_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? 'all');
        $sort = trim($_GET['sort'] ?? 'recent');
        $stories = $this->listAdminWithFilters(['q' => $q, 'status' => $status, 'sort' => $sort]);
        
        extract(['stories' => $stories, 'q' => $q, 'status' => $status, 'sort' => $sort]);
        $viewFile = __DIR__ . '/../Views/admin/stories.php';
        include $viewFile;
    }
    
    public function approve(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $this->approveStory($id);
        }
        header('Location: /projetweb/ablelink/admin');
    }
    
    public function reject(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
       
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $this->rejectStory($id);
        }
        header('Location: /projetweb/ablelink/admin');
    }

    public function bulk(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $action = trim($_POST['action'] ?? '');
        $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
        
        if ($action === 'approve') { $this->updateStatusByIds($ids, 'approved'); }
        elseif ($action === 'reject') { $this->updateStatusByIds($ids, 'rejected'); }
        elseif ($action === 'delete') { $this->deleteByIds($ids); }
        
        header('Location: /projetweb/ablelink/admin/all-stories');
    }

    public function reported(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $comments = $this->reportedComments(100);
        extract(['comments' => $comments]);
        $viewFile = __DIR__ . '/../Views/admin/reported.php';
        include $viewFile;
    }

    public function comments(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $story_id = (int)($_GET['story_id'] ?? 0);
        $reported = trim($_GET['reported'] ?? 'all');
        $sort = trim($_GET['sort'] ?? 'recent');
        $comments = $this->listAllComments([
            'story_id' => $story_id,
            'reported' => $reported === 'all' ? '' : $reported,
            'sort' => $sort
        ]);
        
        extract(['comments' => $comments, 'story_id' => $story_id, 'reported' => $reported, 'sort' => $sort]);
        $viewFile = __DIR__ . '/../Views/admin/comments.php';
        include $viewFile;
    }

    public function unreport(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $id = (int)($_GET['id'] ?? 0);
        $return = trim($_GET['return'] ?? '');
        $story_id = (int)($_GET['story_id'] ?? 0);
        $reported = trim($_GET['reported'] ?? 'all');
        
        if ($id) { $this->unreportComment($id); }
        
        if ($return === 'comments') {
            $url = '/projetweb/ablelink/admin/comments?reported=' . urlencode($reported);
            if ($story_id > 0) { $url .= '&story_id=' . $story_id; }
            header('Location: ' . $url);
        } elseif ($return === 'story' && $story_id > 0) {
            header('Location: /projetweb/ablelink/admin/story?id=' . $story_id);
        } else {
            header('Location: /projetweb/ablelink/admin/reported');
        }
    }

    public function deleteComment(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $id = (int)($_GET['id'] ?? 0);
        $return = trim($_GET['return'] ?? '');
        $story_id = (int)($_GET['story_id'] ?? 0);
        $reported = trim($_GET['reported'] ?? 'all');
        
        if ($id) { $this->deleteCommentById($id); }
        
        if ($return === 'comments') {
            $url = '/projetweb/ablelink/admin/comments?reported=' . urlencode($reported);
            if ($story_id > 0) { $url .= '&story_id=' . $story_id; }
            header('Location: ' . $url);
        } elseif ($return === 'story' && $story_id > 0) {
            header('Location: /projetweb/ablelink/admin/story?id=' . $story_id);
        } else {
            header('Location: /projetweb/ablelink/admin/reported');
        }
    }

    public function story(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $id = (int)($_GET['id'] ?? 0);
        $story = $this->findStory($id);
        if (!$story) { header('Location: /projetweb/ablelink/admin/all-stories'); return; }
        
        $sort = trim($_GET['sort'] ?? 'recent');
        $comments = $this->commentsForStory($id, $sort);
        
        extract(['story' => $story, 'comments' => $comments, 'sort' => $sort]);
        $viewFile = __DIR__ . '/../Views/admin/story.php';
        include $viewFile;
    }

    public function exportStories(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $q = trim($_GET['q'] ?? '');
        $status = trim($_GET['status'] ?? 'all');
        $sort = trim($_GET['sort'] ?? 'recent');
        $start = trim($_GET['start'] ?? '');
        $end = trim($_GET['end'] ?? '');
        $include = trim($_GET['include'] ?? '');
        $includeSet = array_filter(array_map('trim', explode(',', $include)));
        
        $stories = $this->listAdminWithFilters([
            'q' => $q,
            'status' => $status,
            'sort' => $sort,
            'start' => $start,
            'end' => $end
        ]);
        
        header('Content-Type: text/csv; charset=utf-8');
        $filename = 'stories_export_' . date('Ymd_His') . '.csv';
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        $header = ['id','title','author','likes','status','created_at'];
        if (in_array('image', $includeSet, true)) $header[] = 'image';
        if (in_array('video', $includeSet, true)) $header[] = 'video_url';
        if (in_array('content', $includeSet, true)) $header[] = 'content';
        fputcsv($out, $header);
        foreach ($stories as $s) {
            $row = [ $s['id'] ?? '', $s['title'] ?? '', $s['author'] ?? '', $s['likes'] ?? 0, $s['status'] ?? '', $s['created_at'] ?? '' ];
            if (in_array('image', $includeSet, true)) $row[] = $s['image'] ?? '';
            if (in_array('video', $includeSet, true)) $row[] = $s['video_url'] ?? '';
            if (in_array('content', $includeSet, true)) $row[] = $s['content'] ?? ($s['description'] ?? '');
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }

    public function stats(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
        if (empty($_SESSION['admin_logged_in'])) { header('Location: /projetweb/ablelink/admin_login.php'); return; }
        
        $status = $this->statusCounts();
        $cats = $this->categoryDistribution();
        $monthsApproved = $this->monthlyProgression(6, 'approved');
        $monthsPending = $this->monthlyProgression(6, 'pending');
        $monthsRejected = $this->monthlyProgression(6, 'rejected');
        
        extract([
            'status' => $status,
            'cats' => $cats,
            'monthsApproved' => $monthsApproved,
            'monthsPending' => $monthsPending,
            'monthsRejected' => $monthsRejected
        ]);
        $viewFile = __DIR__ . '/../Views/admin/stats.php';
        include $viewFile;
    }
}
