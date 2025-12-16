<?php
namespace App\Models;

class Participation {
    private ?int $id;
    private int $eventId;
    private string $userName;
    private string $status;
    private string $createdAt;
    
    public function __construct(
        ?int $id = null,
        int $eventId = 0,
        string $userName = '',
        string $status = 'registered',
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->userName = $userName;
        $this->status = $status;
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
    
    public function setEventId(int $eventId): void {
        $this->eventId = $eventId;
    }
    
    public function setUserName(string $userName): void {
        $this->userName = $userName;
    }
    
    public function setStatus(string $status): void {
        $this->status = $status;
    }
    
    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }
}
