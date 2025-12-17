<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class SuccessStoriesController extends Controller {
    private \PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
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
    
    private function authorField(): string {
        if ($this->hasColumn('success_stories', 'author')) return 'author';
        if ($this->hasColumn('success_stories', 'author_name')) return 'author_name';
        return 'author';
    }
    
    private function ensureStatusColumn(): void {
        if (!$this->hasColumn('success_stories', 'status')) {
            try {
                $this->pdo->exec('ALTER TABLE `success_stories` ADD COLUMN `status` ENUM("pending","approved","rejected") DEFAULT "pending" AFTER `likes`');
            } catch (\PDOException $e) { }
        }
    }
    
    private function ensureImageColumn(): void {
        if (!$this->hasColumn('success_stories', 'image')) {
            try { $this->pdo->exec('ALTER TABLE `success_stories` ADD COLUMN `image` VARCHAR(255) NULL AFTER `video_url`'); } catch (\PDOException $e) { }
        }
        if (!$this->hasColumn('success_stories', 'video_url')) {
            try { $this->pdo->exec('ALTER TABLE `success_stories` ADD COLUMN `video_url` VARCHAR(255) NULL'); } catch (\PDOException $e) { }
        }
    }
    
    private function ensureSharesColumn(): void {
        if (!$this->hasColumn('success_stories', 'shares')) {
            try { $this->pdo->exec('ALTER TABLE `success_stories` ADD COLUMN `shares` INT DEFAULT 0 AFTER `likes`'); } catch (\PDOException $e) { }
        }
    }
    
    // ========== AUTHENTICATION HELPERS ==========
    
    private function requireLogin(): void {
        if (empty($_SESSION['user_logged_in'])) {
            header('Location: /projetweb/ablelink/auth/login');
            exit;
        }
    }
    
    private function isLoggedIn(): bool {
        return !empty($_SESSION['user_logged_in']);
    }
    
    private function isAdmin(): bool {
        return !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    private function getUserId(): ?int {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }
    
    private function canEditStory(?array $story): bool {
        if (!$story) return false;
        if ($this->isAdmin()) return true;
        $userId = $this->getUserId();
        if (!$userId) return false;
        return isset($story['user_id']) && $story['user_id'] == $userId;
    }
    
    private function listPublicWithFilters(array $opts = []): array {
        $q = trim((string)($opts['q'] ?? ''));
        $sort = (string)($opts['sort'] ?? 'recent');
        $author = trim((string)($opts['author'] ?? ''));
        $cat = trim((string)($opts['cat'] ?? ''));
        $limit = isset($opts['limit']) ? (int)$opts['limit'] : 0;
        $offset = isset($opts['offset']) ? (int)$opts['offset'] : 0;
        $hasStatus = $this->hasColumn('success_stories', 'status');
        $hasCategory = $this->hasColumn('success_stories', 'category');
        $contentCol = $this->contentField();
        $authorCol = $this->authorField();
        $where = [];
        $params = [];
        if ($hasStatus) { $where[] = 'status = "approved"'; }
        if ($q !== '') { $where[] = '(title LIKE ? OR ' . $contentCol . ' LIKE ? OR ' . $authorCol . ' LIKE ?)'; $like = '%'.$q.'%'; $params[] = $like; $params[] = $like; $params[] = $like; }
        if ($author !== '') { $where[] = $authorCol . ' LIKE ?'; $params[] = '%'.$author.'%'; }
        if ($hasCategory && $cat !== '') { $where[] = 'category = ?'; $params[] = $cat; }
        $order = 'created_at DESC';
        if ($sort === 'likes') { $order = 'likes DESC, created_at DESC'; }
        elseif ($sort === 'title') { $order = 'title ASC'; }
        $sql = 'SELECT * FROM success_stories';
        if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
        $sql .= ' ORDER BY ' . $order;
        if ($limit > 0) { $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $offset; }
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) { return []; }
    }
    
    private function countPublicWithFilters(array $opts = []): int {
        $q = trim((string)($opts['q'] ?? ''));
        $author = trim((string)($opts['author'] ?? ''));
        $cat = trim((string)($opts['cat'] ?? ''));
        $hasStatus = $this->hasColumn('success_stories', 'status');
        $hasCategory = $this->hasColumn('success_stories', 'category');
        $contentCol = $this->contentField();
        $authorCol = $this->authorField();
        $where = [];
        $params = [];
        if ($hasStatus) { $where[] = 'status = "approved"'; }
        if ($q !== '') { $where[] = '(title LIKE ? OR ' . $contentCol . ' LIKE ? OR ' . $authorCol . ' LIKE ?)'; $like = '%'.$q.'%'; $params[] = $like; $params[] = $like; $params[] = $like; }
        if ($author !== '') { $where[] = $authorCol . ' LIKE ?'; $params[] = '%'.$author.'%'; }
        if ($hasCategory && $cat !== '') { $where[] = 'category = ?'; $params[] = $cat; }
        $sql = 'SELECT COUNT(*) AS c FROM success_stories';
        if (!empty($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
        try { $stmt = $this->pdo->prepare($sql); $stmt->execute($params); $row = $stmt->fetch(\PDO::FETCH_ASSOC); return (int)($row['c'] ?? 0); } catch (\PDOException $e) { return 0; }
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
    
    private function createStory(string $title, string $author, string $content, string $videoUrl = '', string $image = '', ?int $userId = null): int {
        try {
            $this->ensureStatusColumn();
            $this->ensureImageColumn();
            $this->ensureSharesColumn();
            $contentCol = $this->contentField();
            $cols = ['title','author', $contentCol];
            $vals = [$title, $author, $content];
            if ($this->hasColumn('success_stories', 'user_id') && $userId) { $cols[] = 'user_id'; $vals[] = $userId; }
            if ($this->hasColumn('success_stories', 'likes')) { $cols[] = 'likes'; $vals[] = 0; }
            if ($this->hasColumn('success_stories', 'shares')) { $cols[] = 'shares'; $vals[] = 0; }
            if ($this->hasColumn('success_stories', 'status')) { $cols[] = 'status'; $vals[] = 'pending'; }
            if ($this->hasColumn('success_stories', 'created_at')) { $cols[] = 'created_at'; $vals[] = date('Y-m-d H:i:s'); }
            if ($this->hasColumn('success_stories', 'video_url')) { $cols[] = 'video_url'; $vals[] = $videoUrl; }
            if ($this->hasColumn('success_stories', 'image')) { $cols[] = 'image'; $vals[] = $image; }
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            $sql = 'INSERT INTO success_stories (' . implode(',', $cols) . ') VALUES (' . $placeholders . ')';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($vals);
            return (int)$this->pdo->lastInsertId();
        } catch (\PDOException $e) { return 0; }
    }
    
    private function updateStory(int $id, string $title, string $author, string $content, string $videoUrl = '', string $image = ''): bool {
        try {
            $this->ensureImageColumn();
            $contentCol = $this->contentField();
            $sets = ['title = ?', 'author = ?', $contentCol . ' = ?'];
            $params = [$title, $author, $content];
            if ($this->hasColumn('success_stories', 'video_url')) { $sets[] = 'video_url = ?'; $params[] = $videoUrl; }
            if ($this->hasColumn('success_stories', 'image')) { $sets[] = 'image = ?'; $params[] = $image; }
            $sql = 'UPDATE success_stories SET ' . implode(', ', $sets) . ' WHERE id = ?';
            $params[] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) { return false; }
    }
    
    private function deleteStory(int $id): bool {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM success_stories WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function likeStory(int $id): bool {
        try {
            $stmt = $this->pdo->prepare('UPDATE success_stories SET likes = likes + 1 WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function shareStory(int $id): bool {
        try {
            $this->ensureSharesColumn();
            $stmt = $this->pdo->prepare('UPDATE success_stories SET shares = COALESCE(shares,0) + 1 WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) { return false; }
    }
    
    private function relatedStories(int $id, int $limit = 3): array {
        try {
            $current = $this->findStory($id);
            if (!$current) return [];
            $hasCategory = $this->hasColumn('success_stories', 'category');
            $params = [];
            if ($hasCategory && !empty($current['category'])) {
                $sql = 'SELECT * FROM success_stories WHERE id <> ? AND category = ? ORDER BY likes DESC, created_at DESC LIMIT ' . (int)$limit;
                $params = [$id, $current['category']];
            } else {
                $sql = 'SELECT * FROM success_stories WHERE id <> ? AND author = ? ORDER BY likes DESC, created_at DESC LIMIT ' . (int)$limit;
                $params = [$id, $current['author'] ?? ''];
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$rows) {
                $stmt = $this->pdo->prepare('SELECT * FROM success_stories WHERE id <> ? ORDER BY created_at DESC LIMIT ' . (int)$limit);
                $stmt->execute([$id]);
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            return $rows;
        } catch (\PDOException $e) { return []; }
    }
    
    private function allForAdmin(): array {
        try {
            $stmt = $this->pdo->query('SELECT * FROM success_stories ORDER BY created_at DESC');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    private function updateStoryStatus(int $id, string $status): bool {
        try {
            $this->ensureStatusColumn();
            $stmt = $this->pdo->prepare('UPDATE success_stories SET status = ? WHERE id = ?');
            return $stmt->execute([$status, $id]);
        } catch (\PDOException $e) { return false; }
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
        } catch (\PDOException $e) { return ['approved'=>0,'pending'=>0,'rejected'=>0,'total'=>0]; }
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
        } catch (\PDOException $e) { return []; }
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
        } catch (\PDOException $e) { return []; }
    }
    
    private function createComment(int $storyId, string $author, string $content): int {
        try {
            $this->ensureCommentColumns();
            if ($this->hasColumn('comments', 'content')) {
                $stmt = $this->pdo->prepare('INSERT INTO comments (story_id, author, content, likes, created_at) VALUES (?, ?, ?, 0, NOW())');
                $stmt->execute([$storyId, $author, $content]);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO comments (story_id, author, comment_text) VALUES (?, ?, ?)');
                $stmt->execute([$storyId, $author, $content]);
            }
            return (int)$this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    private function createCommentReply(int $storyId, int $parentId, string $author, string $content): int {
        try {
            $this->ensureCommentColumns();
            if ($this->hasColumn('comments', 'content')) {
                $stmt = $this->pdo->prepare('INSERT INTO comments (story_id, parent_id, author, content, likes, created_at) VALUES (?, ?, ?, ?, 0, NOW())');
                $stmt->execute([$storyId, $parentId, $author, $content]);
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO comments (story_id, parent_id, author, comment_text) VALUES (?, ?, ?, ?)');
                $stmt->execute([$storyId, $parentId, $author, $content]);
            }
            return (int)$this->pdo->lastInsertId();
        } catch (\PDOException $e) { return 0; }
    }
    
    private function deleteComment(int $id): bool {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    private function likeComment(int $id): bool {
        try {
            $this->ensureCommentColumns();
            $stmt = $this->pdo->prepare('UPDATE comments SET likes = likes + 1 WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) { return false; }
    }
    
    private function reportComment(int $id): bool {
        try {
            $this->ensureCommentColumns();
            $stmt = $this->pdo->prepare('UPDATE comments SET reported = 1 WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) { return false; }
    }
    
    // ========== PUBLIC CONTROLLER ACTIONS ==========
    
    public function index(): void {
        $q = trim($_GET['q'] ?? '');
        $sort = trim($_GET['sort'] ?? 'recent');
        $author = trim($_GET['author'] ?? '');
        $cat = trim($_GET['cat'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per = max(6, (int)($_GET['per'] ?? 6));
        
        $total = $this->countPublicWithFilters(['q' => $q, 'author' => $author, 'cat' => $cat]);
        $stories = $this->listPublicWithFilters([
            'q' => $q,
            'sort' => $sort,
            'author' => $author,
            'cat' => $cat,
            'limit' => $per,
            'offset' => ($page - 1) * $per,
        ]);
        
        $this->render('success_stories/index', [
            'stories' => $stories,
            'q' => $q,
            'sort' => $sort,
            'author' => $author,
            'cat' => $cat,
            'page' => $page,
            'per' => $per,
            'total' => $total
        ]);
    }

    public function create(): void {
        $this->requireLogin();
        $this->render('success_stories/form');
    }

    public function store(): void {
        $this->requireLogin();
        
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '') ?: ($_SESSION['user_name'] ?? 'Anonyme');
        $content = trim($_POST['content'] ?? '');
        $videoUrl = trim($_POST['video_url'] ?? '');
        $imagePath = '';
        
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../img/uploads';
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
            $name = preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['image']['name']));
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $nameSafe = uniqid('story_', true) . ($ext ? '.' . strtolower($ext) : '');
            $target = $uploadDir . '/' . $nameSafe;
            if (@move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imagePath = '/projetweb/ablelink/img/uploads/' . $nameSafe;
            }
        }
        
        if ($title && $author && $content) {
            $userId = $this->getUserId();
            $this->createStory($title, $author, $content, $videoUrl, $imagePath, $userId);
        }
        
        header('Location: /projetweb/ablelink/success-stories');
    }

    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $story = $this->findStory($id);
        
        if (!$story) { 
            header('Location: /projetweb/ablelink/success-stories'); 
            return; 
        }
        
        if (!$this->canEditStory($story)) {
            header('Location: /projetweb/ablelink/success-stories');
            return;
        }
        
        $this->render('success_stories/form', ['story' => $story]);
    }

    public function update(): void {
        $id = (int)($_POST['id'] ?? 0);
        $story = $this->findStory($id);
        
        if (!$this->canEditStory($story)) {
            header('Location: /projetweb/ablelink/success-stories');
            return;
        }
        
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $videoUrl = trim($_POST['video_url'] ?? '');
        $imagePath = trim($_POST['current_image'] ?? ($story['image'] ?? ''));
        
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../img/uploads';
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
            $name = preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['image']['name']));
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $nameSafe = uniqid('story_', true) . ($ext ? '.' . strtolower($ext) : '');
            $target = $uploadDir . '/' . $nameSafe;
            if (@move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imagePath = '/projetweb/ablelink/img/uploads/' . $nameSafe;
            }
        }
        
        if ($id && $title && $author && $content) {
            $this->updateStory($id, $title, $author, $content, $videoUrl, $imagePath);
        }
        
        header('Location: /projetweb/ablelink/success-stories');
    }

    public function updateStatus(): void {
        if (!$this->isAdmin()) {
            header('Location: /projetweb/ablelink/auth/login');
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        $status = trim($_GET['status'] ?? '');
        
        $validStatuses = ['approved', 'rejected', 'pending'];
        
        if ($id && in_array($status, $validStatuses)) {
            $this->updateStoryStatus($id, $status);
        }
        
        // Return to admin history with same filters
        $q = trim($_GET['q'] ?? '');
        $statusFilter = trim($_GET['f_status'] ?? ''); // existing filter
        $url = '/projetweb/ablelink/historique?q=' . urlencode($q) . '&status=' . urlencode($statusFilter);
        
        header('Location: ' . $url);
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $story = $this->findStory($id);
        
        if ($story && $this->canEditStory($story)) {
            $this->deleteStory($id);
        }
        
        $ret = trim($_GET['return'] ?? '');
        if ($ret === 'admin') {
            $q = trim($_GET['q'] ?? '');
            $status = trim($_GET['status'] ?? 'all');
            $sort = trim($_GET['sort'] ?? 'recent');
            $page = trim($_GET['page'] ?? '');
            $per = trim($_GET['per'] ?? '');
            $url = '/projetweb/ablelink/admin/all-stories?status=' . urlencode($status) . '&sort=' . urlencode($sort) . '&q=' . urlencode($q);
            if ($page !== '') { $url .= '&page=' . urlencode($page); }
            if ($per !== '') { $url .= '&per=' . urlencode($per); }
            header('Location: ' . $url);
        } else {
            header('Location: /projetweb/ablelink/success-stories');
        }
    }

    public function like(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id) { $this->likeStory($id); }
        
        $return = $_GET['return'] ?? '';
        if ($return === 'comments') {
            header('Location: /projetweb/ablelink/success-stories/comments?id=' . $id);
        } else {
            header('Location: /projetweb/ablelink/success-stories');
        }
    }

    public function share(): void {
        $id = (int)($_GET['id'] ?? 0);
        $platform = trim($_GET['platform'] ?? '');
        
        if ($id) { $this->shareStory($id); }
        $story = $this->findStory($id);
        
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $detailUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/projetweb/ablelink/success-stories/comments?id=' . $id;
        $urlEnc = urlencode($detailUrl);
        $titleEnc = urlencode($story['title'] ?? 'AbleLink');
        $redir = '/projetweb/ablelink/success-stories/comments?id=' . $id;
        
        if ($platform === 'facebook') {
            $redir = 'https://www.facebook.com/sharer/sharer.php?u=' . $urlEnc;
        } elseif ($platform === 'twitter') {
            $redir = 'https://twitter.com/intent/tweet?url=' . $urlEnc . '&text=' . $titleEnc;
        } elseif ($platform === 'linkedin') {
            $redir = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $urlEnc;
        } elseif ($platform === 'whatsapp') {
            $redir = 'https://api.whatsapp.com/send?text=' . $titleEnc . '%20' . $urlEnc;
        }
        
        header('Location: ' . $redir);
    }

    public function comments(): void {
        $id = (int)($_GET['id'] ?? 0);
        $story = $this->findStory($id);
        if (!$story) { header('Location: /projetweb/ablelink/success-stories'); return; }
        
        $sort = $_GET['sort'] ?? 'recent';
        $comments = $this->commentsForStory($id, $sort);
        $related = $this->relatedStories($id, 3);
        
        $text = isset($story['content']) ? (string)$story['content'] : (string)($story['description'] ?? '');
        if (function_exists('mb_strimwidth')) {
            $desc = mb_strimwidth($text, 0, 180, '...');
        } else {
            $desc = strlen($text) > 180 ? substr($text, 0, 180) . '...' : $text;
        }
        
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/projetweb/ablelink/success-stories/comments?id=' . $id;
        
        $this->render('success_stories/comments', [
            'story' => $story,
            'comments' => $comments,
            'related' => $related,
            'sort' => $sort,
            'og_title' => $story['title'] ?? 'AbleLink',
            'og_description' => $desc,
            'og_image' => '/projetweb/ablelink/img/logo/logo-1.png',
            'og_url' => $url,
        ]);
    }

    public function commentStore(): void {
        if (session_status() !== \PHP_SESSION_ACTIVE) { session_start(); }
        
        $storyId = (int)($_POST['story_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $parentId = (int)($_POST['parent_id'] ?? 0);
        
        $sessAuthor = '';
        if (!empty($_SESSION['user_logged_in'])) {
            $sessAuthor = trim((string)($_SESSION['user_name'] ?? $_SESSION['user_email'] ?? ''));
        }
        $authorPost = trim((string)($_POST['author'] ?? ''));
        $author = $sessAuthor !== '' ? $sessAuthor : $authorPost;
        if ($author === '') { $author = 'Invité'; }
        
        if ($storyId && $content !== '') {
            if ($parentId) {
                $this->createCommentReply($storyId, $parentId, $author, $content);
            } else {
                $this->createComment($storyId, $author, $content);
            }
        }
        
        header('Location: /projetweb/ablelink/success-stories/comments?id=' . $storyId);
    }

    public function commentDelete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $storyId = (int)($_GET['story_id'] ?? 0);
        if ($id) { $this->deleteComment($id); }
        header('Location: /projetweb/ablelink/success-stories/comments?id=' . $storyId);
    }

    public function commentLike(): void {
        $id = (int)($_GET['id'] ?? 0);
        $storyId = (int)($_GET['story_id'] ?? 0);
        if ($id) { $this->likeComment($id); }
        header('Location: /projetweb/ablelink/success-stories/comments?id=' . $storyId);
    }

    public function commentReport(): void {
        $id = (int)($_GET['id'] ?? 0);
        $storyId = (int)($_GET['story_id'] ?? 0);
        if ($id) { $this->reportComment($id); }
        header('Location: /projetweb/ablelink/success-stories/comments?id=' . $storyId);
    }

    public function history(): void {
        $stats = $this->statusCounts();
        
        // Filters
        $q = trim($_GET['q'] ?? '');
        $statusFilter = trim($_GET['status'] ?? '');
        $author = trim($_GET['author'] ?? '');
        $category = trim($_GET['category'] ?? '');
        
        // Get all stories
        $allStories = $this->allForAdmin();
        
        // LOG DEBUG
        $logFile = 'C:/xampp/htdocs/projetweb/ablelink/debug_log.txt';
        $log = "--- REQUEST " . date('Y-m-d H:i:s') . " ---\n";
        $log .= "Params: q='$q', status='$statusFilter', author='$author', cat='$category'\n";
        $log .= "Total stories: " . count($allStories) . "\n";
        
        // Apply filters progressively
        $stories = $allStories;
        
        // Filter by Search (Title/Author/Description)
        if (!empty($q)) {
            $log .= "Filtering by Q: '$q'\n";
            $stories = array_filter($stories, function($s) use ($q, &$log) {
                $title = (string)($s['title'] ?? '');
                $authorName = (string)($s['author'] ?? $s['author_name'] ?? '');
                $description = (string)($s['description'] ?? $s['content'] ?? '');
                
                $match = stripos($title, $q) !== false 
                    || stripos($authorName, $q) !== false
                    || stripos($description, $q) !== false;
                
                $log .= "  ID " . ($s['id']??'?') . " ('$title') vs '$q': " . ($match ? "MATCH" : "NO") . "\n";
                return $match;
            });
        }
        
        // Filter by Status
        if (!empty($statusFilter)) {
            $log .= "Filtering by Status: '$statusFilter'\n";
            $stories = array_filter($stories, function($s) use ($statusFilter) {
                $st = $s['status'] ?? 'pending';
                return $st === $statusFilter;
            });
        }
        
        // Filter by Author
        if (!empty($author)) {
            $log .= "Filtering by Author: '$author'\n";
            $stories = array_filter($stories, function($s) use ($author) {
                $a = (string)($s['author'] ?? $s['author_name'] ?? '');
                return stripos($a, $author) !== false;
            });
        }
        
        if (!empty($category)) {
            $stories = array_filter($stories, function($s) use ($category) {
                return stripos((string)($s['category'] ?? ''), $category) !== false;
            });
        }
        
        // Reindex array
        $stories = array_values($stories);
        $log .= "Final count: " . count($stories) . "\n";
        @file_put_contents($logFile, $log, FILE_APPEND);
        
        $this->render('success_stories/history', [
            'stats' => $stats,
            'stories' => $stories,
            'q' => $q,
            'statusFilter' => $statusFilter,
            'author' => $author,
            'category' => $category
        ]);
    }
}
