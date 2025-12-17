<?php
namespace App\Models;

class Evaluation {
    private ?int $id;
    private int $eventId;
    private string $userName;
    private int $rating;
    private string $comment;
    private string $createdAt;
    
    public function __construct(
        ?int $id = null,
        int $eventId = 0,
        string $userName = '',
        int $rating = 0,
        string $comment = '',
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->userName = $userName;
        $this->rating = $rating;
        $this->comment = $comment;
        $this->createdAt = $createdAt;
    }
    
    // Getters
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getEventId(): int {
        return $this->eventId;
    }
    
    public function getUserName(): string {
        return $this->userName;
    }
    
    public function getRating(): int {
        return $this->rating;
    }
    
    public function getComment(): string {
        return $this->comment;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }
    
    public function setEventId(int $eventId): void {
        $this->eventId = $eventId;
    }
    
    public function setUserName(string $userName): void {
        $this->userName = $userName;
    }
    
    public function setRating(int $rating): void {
        $this->rating = $rating;
    }
    
    public function setComment(string $comment): void {
        $this->comment = $comment;
    }
    
    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }
}
