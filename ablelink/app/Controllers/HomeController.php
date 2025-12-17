<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class HomeController extends Controller {
    private \PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    
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
    
    // Get latest approved stories for public homepage
    private function getLatestStories(int $limit = 6): array {
        try {
            $hasStatus = $this->hasColumn('success_stories', 'status');
            $contentCol = $this->contentField();
            
            // Ensure limit is an integer
            $limit = max(1, min(100, (int)$limit));
            
            if ($hasStatus) {
                $sql = "SELECT * FROM success_stories WHERE status = 'approved' ORDER BY created_at DESC LIMIT " . $limit;
            } else {
                $sql = "SELECT * FROM success_stories ORDER BY created_at DESC LIMIT " . $limit;
            }
            
            $stmt = $this->pdo->query($sql);
            $stories = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Debug: log how many stories were found
            error_log("HomeController: Found " . count($stories) . " stories (hasStatus: " . ($hasStatus ? 'yes' : 'no') . ")");
            
            return $stories;
        } catch (\PDOException $e) {
            error_log("HomeController error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get some recent public comments
    private function getRecentComments(int $limit = 5): array {
        try {
            $hasContent = $this->hasColumn('comments', 'content');
            $hasReported = $this->hasColumn('comments', 'reported');
            $select = $hasContent ? 'c.content' : 'c.comment_text';
            
            // Ensure limit is an integer
            $limit = max(1, min(100, (int)$limit));
            
            $sql = "SELECT c.id, c.author, $select AS content, c.created_at, s.title AS story_title, s.id AS story_id
                    FROM comments c
                    INNER JOIN success_stories s ON s.id = c.story_id";
            
            if ($hasReported) {
                $sql .= " WHERE c.reported = 0";
            }
            
            $sql .= " ORDER BY c.created_at DESC LIMIT " . $limit;
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    // Get comments for specific stories
    private function getCommentsForStories(array $storyIds): array {
        if (empty($storyIds)) return [];
        
        try {
            $hasContent = $this->hasColumn('comments', 'content');
            $select = $hasContent ? 'content' : 'comment_text';
            
            $placeholders = implode(',', array_fill(0, count($storyIds), '?'));
            $sql = "SELECT id, story_id, author, $select AS content, created_at, likes 
                    FROM comments 
                    WHERE story_id IN ($placeholders) AND (parent_id IS NULL OR parent_id = 0) AND reported = 0
                    ORDER BY created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($storyIds);
            $comments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Group comments by story_id
            $grouped = [];
            foreach ($comments as $comment) {
                $storyId = $comment['story_id'];
                if (!isset($grouped[$storyId])) {
                    $grouped[$storyId] = [];
                }
                $grouped[$storyId][] = $comment;
            }
            
            return $grouped;
        } catch (\PDOException $e) {
            return [];
        }
    }
    
    public function index(): void {
        // Get 6 latest approved stories
        $stories = $this->getLatestStories(6);
        
        // Get comments for these stories
        $storyIds = array_column($stories, 'id');
        $commentsByStory = $this->getCommentsForStories($storyIds);
        
        // Get 5 recent comments
        $recentComments = $this->getRecentComments(5);
        
        $this->render('home/index', [
            'stories' => $stories,
            'commentsByStory' => $commentsByStory,
            'recentComments' => $recentComments
        ]);
    }
}
