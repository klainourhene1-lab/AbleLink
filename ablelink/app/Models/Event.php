<?php
namespace App\Models;

class Event {
    private ?int $id;
    private string $title;
    private string $company;
    private string $description;
    private string $accessibilityType;
    private string $location;
    private string $startAt;
    private string $endAt;
    private string $status;
    private string $createdAt;
    
    public function __construct(
        ?int $id = null,
        string $title = '',
        string $company = '',
        string $description = '',
        string $accessibilityType = '',
        string $location = '',
        string $startAt = '',
        string $endAt = '',
        string $status = 'published',
        string $createdAt = ''
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->company = $company;
        $this->description = $description;
        $this->accessibilityType = $accessibilityType;
        $this->location = $location;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
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
    
    public function getCompany(): string {
        return $this->company;
    }
    
   public function getDescription(): string {
        return $this->description;
    }
    
    public function getAccessibilityType(): string {
        return $this->accessibilityType;
    }
    
    public function getLocation(): string {
        return $this->location;
    }
    
    public function getStartAt(): string {
        return $this->startAt;
    }
    
    public function getEndAt(): string {
        return $this->endAt;
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
    
    public function setCompany(string $company): void {
        $this->company = $company;
    }
    
    public function setDescription(string $description): void {
        $this->description = $description;
    }
    
    public function setAccessibilityType(string $accessibilityType): void {
        $this->accessibilityType = $accessibilityType;
    }
    
    public function setLocation(string $location): void {
        $this->location = $location;
    }
    
    public function setStartAt(string $startAt): void {
        $this->startAt = $startAt;
    }
    
    public function setEndAt(string $endAt): void {
        $this->endAt = $endAt;
    }
    
    public function setStatus(string $status): void {
        $this->status = $status;
    }
    
    public function setCreatedAt(string $createdAt): void {
        $this->createdAt = $createdAt;
    }
}
