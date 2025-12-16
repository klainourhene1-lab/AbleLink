<?php
namespace App\Models;

class SuccessStory {
    private ?int $id;
    private string $title;
    private string $author;
    private string $content;
    private string $videoUrl;
    private string $image;
    private int $likes;
    private int $shares;
    private string $status;
    private string $createdAt;
    
    public function __construct(
        ?int $id = null,
        string $title = '',
        string $author = '',
        string $content = '',
        string $videoUrl = '',
        string $image = '',
        int $likes = 0,
        int $shares = 0,
        string $status = 'pending',
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->content = $content;
        $this->videoUrl = $videoUrl;
        $this->image = $image;
        $this->likes = $likes;
        $this->shares = $shares;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function getAuthor(): string {
        return $this->author;
    }
    
    public function getContent(): string {
        return $this->content;
    }
    
    public function getVideoUrl(): string {
        return $this->videoUrl;
    }
    
    public function getImage(): string {
        return $this->image;
    }
    
    public function getLikes(): int {
        return $this->likes;
    }
    
    public function getShares(): int {
        return $this->shares;
    }
    
    public function getStatus(): string {
        return $this->status;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }
    
    public function setTitle(string $title): void {
        $this->title = $title;
    }
    
    public function setAuthor(string $author): void {
        $this->author = $author;
    }
    
    public function setContent(string $content): void {
        $this->content = $content;
    }
    
    public function setVideoUrl(string $videoUrl): void {
        $this->videoUrl = $videoUrl;
    }
    
    public function setImage(string $image): void {
        $this->image = $image;
    }
    
    public function setLikes(int $likes): void {
        $this->likes = $likes;
    }
    
    public function setShares(int $shares): void {
        $this->shares = $shares;
    }
    
    public function setStatus(string $status): void {
        $this->status = $status;
    }
    
    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }
}
