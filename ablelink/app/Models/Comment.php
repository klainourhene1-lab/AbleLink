<?php
namespace App\Models;

class Comment {
    private ?int $id;
    private int $storyId;
    private ?int $parentId;
    private string $author;
    private string $content;
    private int $likes;
    private bool $reported;
    private string $createdAt;
    
    public function __construct(
        ?int $id = null,
        int $storyId = 0,
        ?int $parentId = null,
        string $author = '',
        string $content = '',
        int $likes = 0,
        bool $reported = false,
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->storyId = $storyId;
        $this->parentId = $parentId;
        $this->author = $author;
        $this->content = $content;
        $this->likes = $likes;
        $this->reported = $reported;
        $this->createdAt = $createdAt;
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getStoryId(): int {
        return $this->storyId;
    }
    
    public function getParentId(): ?int {
        return $this->parentId;
    }
    
    public function getAuthor(): string {
        return $this->author;
    }
    
    public function getContent(): string {
        return $this->content;
    }
    
    public function getLikes(): int {
        return $this->likes;
    }
    
    public function isReported(): bool {
        return $this->reported;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }
    
    public function setStoryId(int $storyId): void {
        $this->storyId = $storyId;
    }
    
    public function setParentId(?int $parentId): void {
        $this->parentId = $parentId;
    }
    
    public function setAuthor(string $author): void {
        $this->author = $author;
    }
    
    public function setContent(string $content): void {
        $this->content = $content;
    }
    
    public function setLikes(int $likes): void {
        $this->likes = $likes;
    }
    
    public function setReported(bool $reported): void {
        $this->reported = $reported;
    }
    
    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }
}
